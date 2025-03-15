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
        Schema::create('membresias', function (Blueprint $table) {
            $table->id(); // id integer [pk, increment]
            $table->string('nombre')->nullable(false); // nombre varchar [not null]
            $table->text('descripcion')->nullable(); // descripcion text
            $table->text('beneficios')->nullable(); // beneficios text
            $table->decimal('descuento_porcentaje', 5, 2)->default(0); // descuento_porcentaje decimal [default: 0]
            $table->decimal('precio', 10, 2)->default(0);
            $table->timestamps(); // created_at timestamp, updated_at timestamp
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membresias');
    }
};
