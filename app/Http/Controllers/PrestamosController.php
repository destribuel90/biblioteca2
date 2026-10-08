<?php

namespace App\Http\Controllers;

use App\Models\Libros;
use App\Models\Prestamos;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrestamosController extends Controller
{
    private const DIAS_PRESTAMO = 7;
    private const MULTA_POR_DIA = 8;   // pesos
    private const MAX_LIBROS    = 5;

    /**
     * Listar préstamos (filtrable por ?id_usuario=).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Prestamos::query()->latest('id');

        if ($request->filled('id_usuario')) {
            $query->where('id_usuario', $request->integer('id_usuario'));
        }

        $prestamos = $query->paginate(10);
        $prestamos->getCollection()->each(fn ($p) => $this->sincronizarMora($p));

        return response()->json($prestamos);
    }

    /**
     * Registrar un préstamo.
     */
    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'id_usuario' => 'required|integer|exists:usuarios,id',
            'id_libro'   => 'required|integer|exists:libros,id',
        ]);

        return DB::transaction(function () use ($datos) {
            // Préstamos activos del usuario, con la mora al día
            $activos = Prestamos::where('id_usuario', $datos['id_usuario'])
                ->whereNull('fecha_devolucion')
                ->lockForUpdate()
                ->get()
                ->each(fn ($p) => $this->sincronizarMora($p));

            if ($activos->contains('estado', 'Atrasado')) {
                return response()->json([
                    'message' => 'El usuario tiene un libro atrasado y no puede pedir más.',
                ], 422);
            }

            $tieneDeuda = Prestamos::where('id_usuario', $datos['id_usuario'])
                ->where('deuda', '>', 0)
                ->exists();

            if ($tieneDeuda) {
                return response()->json([
                    'message' => 'El usuario tiene una multa pendiente y no puede pedir más.',
                ], 422);
            }

            if ($activos->count() >= self::MAX_LIBROS) {
                return response()->json([
                    'message' => 'El usuario ya tiene el máximo de ' . self::MAX_LIBROS . ' libros prestados.',
                ], 422);
            }

            $libro = Libros::lockForUpdate()->findOrFail($datos['id_libro']);

            if ($libro->cantidad_disponible < 1) {
                return response()->json([
                    'message' => 'No hay ejemplares disponibles de este libro.',
                ], 422);
            }

            $libro->decrement('cantidad_disponible');

            $hoy = now()->startOfDay();

            $prestamo = Prestamos::create([
                'id_usuario'     => $datos['id_usuario'],
                'id_libro'       => $datos['id_libro'],
                'fecha_prestamo' => $hoy->toDateString(),
                // Se entrega a más tardar el último día a las 23:59
                'fecha_limite'   => $hoy->copy()->addDays(self::DIAS_PRESTAMO)->toDateString(),
                'estado'         => 'Activo',
                'deuda'          => 0,
            ]);

            return response()->json($prestamo, 201);
        });
    }

    /**
     * Mostrar un préstamo.
     */
    public function show(Prestamos $prestamo): JsonResponse
    {
        return response()->json($this->sincronizarMora($prestamo));
    }

    /**
     * Registrar la devolución de un libro.
     */
    public function devolver(Prestamos $prestamo): JsonResponse
    {
        return DB::transaction(function () use ($prestamo) {
            $prestamo = Prestamos::lockForUpdate()->findOrFail($prestamo->id);

            if ($prestamo->fecha_devolucion !== null) {
                return response()->json([
                    'message' => 'Este préstamo ya fue devuelto.',
                ], 422);
            }

            // Calcula la multa acumulada hasta hoy
            $this->sincronizarMora($prestamo);

            $prestamo->fecha_devolucion = now()->toDateString();
            $prestamo->estado = 'Devuelto';
            $prestamo->save();

            Libros::where('id', $prestamo->id_libro)->increment('cantidad_disponible');

            return response()->json($prestamo);
        });
    }

    /**
     * Quitar la multa de un préstamo. Solo el admin.
     */
    public function condonarMulta(Request $request, Prestamos $prestamo): JsonResponse
    {
        if (! $this->esAdmin($request)) {
            return response()->json([
                'message' => 'Solo el administrador puede quitar multas.',
            ], 403);
        }

        if ($prestamo->fecha_devolucion === null) {
            return response()->json([
                'message' => 'Primero debe registrarse la devolución del libro.',
            ], 422);
        }

        $prestamo->update(['deuda' => 0]);

        return response()->json($prestamo);
    }

    /**
     * Actualiza estado y multa de un préstamo sin devolver:
     * 8 pesos por cada día transcurrido después de la fecha límite.
     */
    private function sincronizarMora(Prestamos $prestamo): Prestamos
    {
        if ($prestamo->fecha_devolucion !== null) {
            return $prestamo;
        }

        $hoy    = now()->startOfDay();
        $limite = Carbon::parse($prestamo->fecha_limite)->startOfDay();

        // Pasadas las 23:59 de fecha_limite, ya es otro día => atraso
        if ($hoy->gt($limite)) {
            $dias = (int) $limite->diffInDays($hoy, true);

            $prestamo->estado = 'Atrasado';
            $prestamo->deuda  = $dias * self::MULTA_POR_DIA;

            if ($prestamo->isDirty()) {
                $prestamo->save();
            }
        }

        return $prestamo;
    }

    /**
     * Ajusta esta condición a cómo identificas al admin en tu tabla usuarios.
     */
    private function esAdmin(Request $request): bool
    {
        return $request->user()?->rol === 'admin';
    }
}