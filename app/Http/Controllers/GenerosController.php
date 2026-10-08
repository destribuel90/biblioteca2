<?php

namespace App\Http\Controllers;

use App\Models\Generos;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GenerosController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Generos::orderBy('nombre_genero')->paginate(10)
        );
    }

    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'nombre_genero' => 'required|string|max:255',
        ]);

        $genero = Generos::create($datos);

        return response()->json($genero, 201);
    }

    public function show(Generos $genero): JsonResponse
    {
        return response()->json($genero);
    }

    public function update(Request $request, Generos $genero): JsonResponse
    {
        $datos = $request->validate([
            'nombre_genero' => 'required|string|max:255',
        ]);

        $genero->update($datos);

        return response()->json($genero);
    }

    public function destroy(Generos $genero): JsonResponse
    {
        $genero->delete();

        return response()->json(null, 204);
    }
}