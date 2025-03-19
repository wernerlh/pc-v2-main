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
        Schema::create('transacciones_casinop', function (Blueprint $table) {
            $table->id();
            // Cambiamos las restricciones para que coincidan con los modelos disponibles
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('empleado_id');
            $table->unsignedBigInteger('sucursal_id');
            $table->dateTime('fecha');
            $table->enum('tipo', ['deposito', 'retiro']);
            $table->decimal('monto', 10, 2);
            $table->text('observacion')->nullable();
            $table->timestamps();

            // Definimos las relaciones con las tablas correctas
            $table->foreign('sucursal_id')->references('id')->on('sucursales');
            
            // La tabla de usuarios puede contener tanto clientes como empleados
            $table->foreign('cliente_id')->references('id')->on('users');
            $table->foreign('empleado_id')->references('empleado_id')->on('empleados');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transacciones_casinop');
    }
};
