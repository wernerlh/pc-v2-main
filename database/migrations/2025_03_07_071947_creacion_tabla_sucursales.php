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
        Schema::create('sucursales', function (Blueprint $table) {
            $table->id(); // Clave primaria
            $table->string('nombre', 100); // Nombre de la sucursal
            $table->string('direccion', 255); // Dirección completa
            $table->string('telefono', 15)->nullable(); // Teléfono de la sucursal
            $table->string('ciudad', 100); // Ciudad
            $table->string('provincia', 100); // Provincia o estado
            $table->string('codigo_postal', 20); // Código postal
            $table->string('pais', 100); // País
            $table->enum('tipo_establecimiento', ['casino', 'hotel', 'mixto'])->default('mixto'); // Tipo de establecimiento
            $table->integer('capacidad'); // Capacidad
            $table->date('fecha_inauguracion'); // Fecha de inauguración
            $table->timestamps(); // Campos created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sucursales');
    }
};