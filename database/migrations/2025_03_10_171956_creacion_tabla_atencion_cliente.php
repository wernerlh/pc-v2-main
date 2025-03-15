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
        Schema::create('atencion_cliente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('user_clientes')->onDelete('cascade');
            $table->foreignId('empleado_id')->nullable()->references('empleado_id')->on('empleados')->onDelete('set null');
            $table->dateTime('fecha_apertura');
            $table->dateTime('fecha_cierre')->nullable();
            $table->enum('tipo', ['consulta', 'queja', 'soporte_tecnico', 'verificacion', 'financiero', 'sugerencia']);
            $table->string('asunto');
            $table->text('descripcion');
            $table->enum('prioridad', ['baja', 'media', 'alta', 'critica'])->default('media');
            $table->enum('estado', ['abierto', 'en_proceso', 'resuelto', 'cerrado', 'escalado'])->default('abierto');
            $table->text('respuesta')->nullable();
            $table->integer('tiempo_respuesta')->nullable();
            $table->integer('calificacion')->nullable();
            $table->text('comentario_calificacion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atencion_cliente');
    }
};
