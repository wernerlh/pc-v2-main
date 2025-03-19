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
        Schema::create('atencion_empleados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empleado_id');
            $table->unsignedBigInteger('supervisor_id')->nullable(); // Quien atiende al empleado
            $table->string('asunto');
            $table->text('descripcion');
            $table->enum('estado', ['pendiente', 'en_proceso', 'resuelto', 'cancelado'])->default('pendiente');
            $table->enum('prioridad', ['baja', 'media', 'alta', 'urgente'])->default('media');
            $table->dateTime('fecha_solicitud');
            $table->dateTime('fecha_atencion')->nullable();
            $table->dateTime('fecha_resolucion')->nullable();
            $table->text('solucion')->nullable();
            $table->timestamps();

            // Claves foráneas
            $table->foreign('empleado_id')->references('id')->on('users');
            $table->foreign('supervisor_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atencion_empleados');
    }
};
