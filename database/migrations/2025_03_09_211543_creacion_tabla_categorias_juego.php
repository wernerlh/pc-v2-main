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
        Schema::create('categorias_juego', function (Blueprint $table) {
            $table->id(); // id integer [pk, increment]
            $table->string('nombre')->nullable(false); // nombre varchar [not null]
            $table->text('descripcion')->nullable(); // descripcion text
            $table->string('imagen_url')->nullable(); // imagen_url varchar
            $table->integer('orden')->nullable(); // orden integer
            $table->enum('estado', ['activo', 'inactivo'])->default('activo'); // estado enum [not null, default: 'activo']
            $table->timestamps(); // created_at timestamp, updated_at timestamp
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias_juego');
    }
};
