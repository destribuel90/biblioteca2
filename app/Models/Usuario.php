<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombres',
        'apellidos',
        'fecha_nacimiento',
        'direccion',
        'correo',
        'password',
        'telefono'
    ];

    protected $hidden = [
        'password'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date'
    ];
}
