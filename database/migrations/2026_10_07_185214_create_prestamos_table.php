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
        Schema::create('prestamos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')->constrained('usuarios'); // Llave Foránea[cite: 4]
            $table->foreignId('id_libro')->constrained('libros'); // Llave Foránea[cite: 4]
            $table->date('fecha_prestamo'); //[cite: 4]
            $table->date('fecha_limite'); //[cite: 4]
            $table->date('fecha_devolucion')->nullable(); // Puede ser NULL si sigue activo[cite: 4]
            $table->string('estado'); // Ej. 'Activo', 'Devuelto', 'Atrasado'[cite: 4]
            $table->decimal('deuda', 8, 2)->default(0); // Multa acumulada[cite: 4]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestamos');
    }
};
