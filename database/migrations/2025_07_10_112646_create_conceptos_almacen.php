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
        Schema::create('conceptos_almacen', function (Blueprint $table) {
            $table->string('clave', 255)->primary();
            $table->string('descripcion', 255);
            $table->string('tipo', 50);
            $table->boolean('visible_inv_fisico')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conceptos_almacen');
    }
};
