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
        Schema::create('recetas_bebidas', function (Blueprint $table) {
            $table->integer('codigo_bebida');
            $table->integer('codigo_principal');
            $table->integer('cantidad_requerida');
            $table->float('ml_requerido');

            //Relaciones

            $table->foreign('codigo_bebida')->references('codigo')->on('ICB_bebidas');
            $table->foreign('codigo_principal')->references('codigo')->on('IPA_inventario_principal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recetas_bebidas');
    }
};
