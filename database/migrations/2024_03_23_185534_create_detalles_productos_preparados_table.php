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
        Schema::create('detalles_productos_preparados', function (Blueprint $table) {
            $table->integer('folio_produccion');
            $table->integer('codigo_insumo')->nullable();
            $table->integer('codigo_materia_prima')->nullable();
            $table->smallInteger('cantidad_requerida')->nullable();
            $table->float('peso_requerido')->nullable();

            /*//Relaciones

            $table->foreign('folio_produccion')->references('folio')->on('produccion');
            $table->foreign('codigo_insumo')->references('codigo')->on('ICO_insumos');
            $table->foreign('codigo_materia_prima')->references('codigo')->on('IPA_inventario_principal');*/
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_productos_preparados');
    }
};
