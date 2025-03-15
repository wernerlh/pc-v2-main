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
        Schema::table('users', function (Blueprint $table) {
            // Agregar la columna 'empleado_id'
            $table->unsignedBigInteger('empleado_id')->nullable()->after('id');
            
            // Establecer la relación de clave foránea
            $table->foreign('empleado_id')->references('empleado_id')->on('empleados')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Eliminar la relación de clave foránea
            $table->dropForeign(['empleado_id']);
            
            // Eliminar la columna 'empleado_id'
            $table->dropColumn('empleado_id');
        });
    }
};