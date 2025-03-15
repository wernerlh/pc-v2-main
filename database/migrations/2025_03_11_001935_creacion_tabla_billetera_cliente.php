php
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
        Schema::create('billetera_cliente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->unique()->constrained('user_clientes')->onDelete('cascade');
            $table->decimal('balance_real', 10, 2)->default(0);
            $table->decimal('balance_rechazadas', 10, 2)->default(0);
            $table->decimal('balance_pendiente', 10, 2)->default(0);
            $table->decimal('total_depositado', 10, 2)->default(0);
            $table->decimal('total_retirado', 10, 2)->default(0);
            $table->decimal('total_ganado', 10, 2)->default(0);
            $table->decimal('total_apostado', 10, 2)->default(0);
            $table->string('moneda')->default('PEN');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billetera_cliente');
    }
};
