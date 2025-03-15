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
        Schema::create('cliente_membresia', function (Blueprint $table) {
            $table->id(); // id integer [pk, increment]
            $table->foreignId('cliente_id')->constrained('user_clientes')->onDelete('cascade'); // cliente_id integer [ref: > user_clientes.id, not null]
            $table->foreignId('membresia_id')->constrained('membresias')->onDelete('cascade'); // membresia_id integer [ref: > membresias.id, not null]
            $table->date('fecha_inicio')->nullable(false); // fecha_inicio date [not null]
            $table->date('fecha_vencimiento')->nullable(); // fecha_vencimiento date
            $table->enum('estado', ['activa', 'inactiva', 'vencida', 'suspendida'])->default('activa'); // estado enum('activa', 'inactiva', 'vencida', 'suspendida') [not null, default: 'activa']
            $table->timestamps(); // created_at timestamp, updated_at timestamp
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cliente_membresia');
    }
};
