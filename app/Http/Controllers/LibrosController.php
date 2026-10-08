<?php

namespace App\Http\Controllers;

use App\Models\Libros;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LibrosController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Libros::orderBy('titulo')->paginate(10)
        );
    }

    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'ISBN'                => 'required|string|max:255|unique:libros,ISBN',
            'titulo'              => 'required|string|max:255',
            'descripcion'         => 'required|string',
            'edicion'             => 'required|integer|min:1',
            'año'                 => 'required|integer|min:0|max:' . date('Y'),
            'paginas'             => 'required|integer|min:1',
            'id_genero'           => 'required|integer|exists:generos,id',
            'id_autor'            => 'required|integer|exists:autores,id',
            'cantidad_disponible' => 'required|integer|min:0',
        ]);

        $libro = Libros::create($datos);

        return response()->json($libro, 201);
    }

    public function show(Libros $libro): JsonResponse
    {
        return response()->json($libro);
    }

    public function update(Request $request, Libros $libro): JsonResponse
    {
        $datos = $request->validate([
            'ISBN'                => ['required', 'string', 'max:255', Rule::unique('libros', 'ISBN')->ignore($libro->id)],
            'titulo'              => 'required|string|max:255',
            'descripcion'         => 'required|string',
            'edicion'             => 'required|integer|min:1',
            'año'                 => 'required|integer|min:0|max:' . date('Y'),
            'paginas'             => 'required|integer|min:1',
            'id_genero'           => 'required|integer|exists:generos,id',
            'id_autor'            => 'required|integer|exists:autores,id',
            'cantidad_disponible' => 'required|integer|min:0',
        ]);

        $libro->update($datos);

        return response()->json($libro);
    }

    public function destroy(Libros $libro): JsonResponse
    {
        $libro->delete();

        return response()->json(null, 204);
    }
}