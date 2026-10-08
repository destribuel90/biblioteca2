<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Libros extends Model
{
    protected $fillable = [
        'ISBN',
        'titulo',
        'descripcion',
        'edicion',
        'año',
        'paginas',
        'id_genero',
        'id_autor',
        'cantidad_disponible'
    ];
}
