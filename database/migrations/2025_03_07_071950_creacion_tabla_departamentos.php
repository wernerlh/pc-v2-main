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
        Schema::create('departamentos', function (Blueprint $table) {
            $table->id(); // Clave primaria, autoincremental
            $table->string('nombre', 255)->nullable(false); // Nombre del departamento, no nulo
            $table->text('descripcion')->nullable(); // Descripción del departamento, puede ser nula
            $table->unsignedBigInteger('gerente_id')->nullable(); // ID del gerente, puede ser nula

            $table->timestamps(); // Campos created_at y updated_at

            // Relación de clave foránea
            $table->foreign('gerente_id')->references('empleado_id')->on('empleados')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departamentos');
    }
};