<?php

namespace App\Http\Controllers;

use App\Models\Autores;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AutoresController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Autores::orderBy('nombre_autor')->paginate(10)
        );
    }

    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'nombre_autor' => 'required|string|max:255',
        ]);

        $autor = Autores::create($datos);

        return response()->json($autor, 201);
    }

    public function show(Autores $autore): JsonResponse
    {
        return response()->json($autore);
    }

    public function update(Request $request, Autores $autore): JsonResponse
    {
        $datos = $request->validate([
            'nombre_autor' => 'required|string|max:255',
        ]);

        $autore->update($datos);

        return response()->json($autore);
    }

    public function destroy(Autores $autore): JsonResponse
    {
        $autore->delete();

        return response()->json(null, 204);
    }
}