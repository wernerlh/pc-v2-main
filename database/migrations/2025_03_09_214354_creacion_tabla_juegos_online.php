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
        Schema::create('juegos_online', function (Blueprint $table) {
            $table->id(); // id integer [pk, increment]
            $table->string('nombre')->nullable(false); // nombre varchar [not null]
            $table->string('imagen_url')->nullable(); // imagen_url varchar
            $table->foreignId('categoria_id')->constrained('categorias_juego')->onDelete('cascade'); // categoria_id integer [ref: > categorias_juego.id, not null]
            $table->text('descripcion')->nullable(); // descripcion text
            $table->string('pagina_juego')->nullable(false); // pagina_juego varchar [not null]
            $table->enum('estado', ['activo', 'inactivo', 'mantenimiento', 'proximamente'])->default('activo'); // estado enum [not null, default: 'activo']
            $table->foreignId('membresia_requerida')->nullable()->constrained('membresias')->onDelete('set null');
            $table->timestamps(); // created_at timestamp, updated_at timestamp
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('juegos_online');
    }
};
