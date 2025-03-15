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
        Schema::create('transacciones_juego', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('user_clientes')->onDelete('cascade');
            $table->foreignId('juego_id')->constrained('juegos_online')->onDelete('cascade');
            $table->dateTime('fecha_hora');
            $table->decimal('monto_apostado', 10, 2);
            $table->decimal('monto_ganado', 10, 2)->default(0);
            $table->enum('tipo_transaccion', ['apuesta', 'ganancia', 'bonificación', 'freespin']);
            $table->decimal('balance_anterior', 10, 2);
            $table->decimal('balance_posterior', 10, 2);
            $table->json('detalles_juego')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transacciones_juego');
    }
};
