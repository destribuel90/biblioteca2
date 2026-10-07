<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function store(Request $request){

        // se validan los datos del usuario
        $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'rquired|string|max:255',
            'fecha_nacimiento' => 'required|date',
            'direccion' =>  'required|string|max:255',
            'correo' => 'required|email|unique:usuarios,correo',
            'password' => 'rquired|min:8',
            'telefomo' => 'nullable|string|max:20'
        ]);
        
        Usuario::create([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'direccion' => $request->direccion,
            'correo' => $request->correo,
            'password' => Hash::make($request->contraseña),
            'telefono' => $request->telefono
        ]);
    }

    public function update(Request $request, Usuario $usuario){
        $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'fecha_nacimiento' => 'required|date',
            'direccion' => 'required|string|max:255',
            'correo' => 'required|email|unique:usuarios, correo' . $usuario->id(),
            'telefono' => 'nullable|string|max:20',
            'password' => 'nullable|min:8'
        ]);

        $datos = $request->except(['contraseña']);
        
        if($request->filled('password')){
            $datos['contraseña'] = Hash::make($request->password);
        }
        
        $usuario->update($datos);

    }

    public function destroy(Usuario $usuario){
        $usuario->delete();
    }
}
