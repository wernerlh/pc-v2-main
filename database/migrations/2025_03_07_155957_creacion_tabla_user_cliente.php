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
        Schema::create('user_clientes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 16)->unique()->regex('/^[a-z0-9]{8,16}$/'); // Campo name con restricciones
            $table->string('nombre_completo', 200); // Combina nombre y apellido
            $table->string('documento_identidad', 20)->unique();
            $table->string('telefono', 15)->nullable();
            $table->string('direccion', 200)->nullable();
            $table->date('fecha_nacimiento');
            $table->json('preferencias')->nullable();
            $table->enum('estado_cuenta', ['activa', 'inactiva', 'suspendida', 'bloqueada'])->default('activa');
            $table->date('fecha_suspension')->nullable(); // Nuevo campo para la fecha de suspensión

            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('cliente_password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('cliente_sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_cliente_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_clientes');
        Schema::dropIfExists('cliente_password_reset_tokens');
        Schema::dropIfExists('cliente_sessions');
    }
};