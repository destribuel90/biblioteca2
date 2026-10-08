<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Método para iniciar sesión y retornar el token + datos
    public function login(Request $request)
    {
        $request->validate([
            'correo'   => 'required|email',
            'password' => 'required|string',
        ]);

        $usuario = Usuario::where('correo', $request->correo)->first();

        if (!$usuario || !Hash::check($request->password, $usuario->password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        // Generar el token Bearer con Sanctum
        $token = $usuario->createToken('auth_token')->plainTextToken;

        // Responder con el token y el perfil mínimo
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user' => [
                'id'     => $usuario->id,
                'correo' => $usuario->correo,
                'rol'    => $usuario->rol,
            ]
        ], 200);
    }

    // Registro de usuario (store actualizado)
    public function store(Request $request)
    {
        $request->validate([
            'nombres'          => 'required|string|max:255',
            'apellidos'        => 'required|string|max:255',
            'fecha_nacimiento' => 'required|date',
            'direccion'        => 'required|string|max:255',
            'correo'           => 'required|email|unique:usuarios,correo',
            'password'         => 'required|min:8',
            'telefono'         => 'nullable|string|max:20',
            'rol'              => 'nullable|string|max:50',
        ]);

        $usuario = Usuario::create([
            'nombres'          => $request->nombres,
            'apellidos'        => $request->apellidos,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'direccion'        => $request->direccion,
            'correo'           => $request->correo,
            'password'         => Hash::make($request->password),
            'telefono'         => $request->telefono,
            'rol'              => $request->rol ?? 'user',
        ]);

        // Crear token de acceso inmediato al registrarse
        $token = $usuario->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'      => 'Usuario registrado con éxito',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user' => [
                'id'     => $usuario->id,
                'correo' => $usuario->correo,
                'rol'    => $usuario->rol,
            ]
        ], 210);
    }

    // Cierre de sesión (eliminar tokens activos)
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente'
        ]);
    }
}