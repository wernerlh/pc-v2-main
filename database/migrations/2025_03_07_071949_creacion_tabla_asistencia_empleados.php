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
        Schema::create('asistencia_empleados', function (Blueprint $table) {
            $table->id('asistencia_id');
            $table->unsignedBigInteger('empleado_id');
            $table->date('fecha');
            $table->time('hora_entrada');
            $table->time('hora_salida')->nullable();
            $table->decimal('horas_trabajadas', 4, 2)->nullable();
            $table->enum('tipo_jornada', ['COMPLETA', 'MEDIA', 'EXTRA']);
            $table->enum('estado', ['PRESENTE', 'AUSENTE', 'TARDANZA', 'PERMISO']);
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('empleado_id')->references('empleado_id')->on('empleados');
            $table->unique(['empleado_id', 'fecha']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencia_empleados');
    }
};
