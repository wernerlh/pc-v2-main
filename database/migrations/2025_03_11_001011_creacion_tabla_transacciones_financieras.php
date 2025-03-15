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
        Schema::create('transacciones_financieras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('user_clientes')->onDelete('cascade');
            $table->decimal('monto', 10, 2);
            $table->enum('tipo', ['deposito', 'retiro', 'ajuste', 'bono', 'cashback']);
            $table->enum('estado', ['pendiente', 'completada', 'rechazada', 'cancelada', 'en_revision'])->default('pendiente');
            $table->string('numero_cuenta_bancaria')->nullable();
            $table->string('banco')->nullable();
            $table->string('titular_cuenta')->nullable();
            $table->string('referencia_transferencia')->nullable();
            $table->dateTime('fecha_solicitud');
            $table->dateTime('fecha_procesamiento')->nullable();
            $table->text('motivo_rechazo')->nullable();
      
            $table->foreignId('revisado_por')->nullable()->constrained('empleados', 'empleado_id')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transacciones_financieras');
    }
};
