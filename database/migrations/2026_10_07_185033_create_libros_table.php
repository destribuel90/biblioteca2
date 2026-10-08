<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('libros', function (Blueprint $table) {
            $table->id();
            $table->string('ISBN')->unique();
            $table->string('titulo');
            $table->text('descripcion');
            $table->integer('edicion');
            $table->integer('año'); 
            $table->integer('paginas'); 
            $table->foreignId('id_genero')->constrained('generos');
            $table->foreignId('id_autor')->constrained('autores');
            $table->integer('cantidad_disponible');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('libros');
    }
};
