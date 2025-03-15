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
        Schema::create('empleados', function (Blueprint $table) {
            $table->id('empleado_id');
            $table->string('nombre_completo', 100);
            $table->string('documento_identidad', 20)->unique();
            $table->string('correo', 100)->unique();
            $table->string('telefono', 15)->nullable();
            $table->string('cargo'); // Cambiado de 'rol' a 'cargo' y convertido a texto
            $table->date('fecha_contratacion');
            $table->date('fecha_nacimiento');
            $table->enum('estado', ['ACTIVO', 'INACTIVO', 'VACACIONES', 'LICENCIA', 'DESPEDIDO']); // Agregado 'DESPEDIDO'
            $table->decimal('salario_base', 10, 2);
            $table->unsignedBigInteger('supervisor_id')->nullable();
            $table->unsignedBigInteger('sucursal_id')->nullable(); // Campo para la sucursal
            $table->unsignedBigInteger('departamento_id')->nullable(); // Campo para el departamento

            $table->timestamps();

            // Relaciones de clave foránea
            $table->foreign('supervisor_id')->references('empleado_id')->on('empleados')->onDelete('set null');
            $table->foreign('sucursal_id')->references('id')->on('sucursales')->onDelete('set null');
            $table->foreign('departamento_id')->references('id')->on('departamentos')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};