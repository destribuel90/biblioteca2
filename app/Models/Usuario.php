<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombres',
        'apellidos',
        'fecha_nacimiento',
        'direccion',
        'correo',
        'password',
        'telefono',
        'rol',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}