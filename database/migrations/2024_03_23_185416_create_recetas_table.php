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
        Schema::create('recetas', function (Blueprint $table) {
            $table->integer('codigo_producto');
            $table->string('codigo_insumo', 50)->nullable();
            $table->integer('codigo_materia_prima')->nullable();
            $table->smallInteger('cantidad_requerida');
            $table->float('peso_requerido');

            //Relaciones

            $table->foreign('codigo_producto')->references('codigo')->on('ICO_productos');
            $table->foreign('codigo_insumo')->references('codigo')->on('ICO_insumos');
            $table->foreign('codigo_materia_prima')->references('codigo')->on('IPA_inventario_principal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recetas');
    }
};
