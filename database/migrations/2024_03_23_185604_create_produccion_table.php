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
        Schema::create('produccion', function (Blueprint $table) {
            $table->integer('folio')->primary()->unsigned();
            $table->integer('codigo_producto');
            $table->integer('id_detalle_venta_producto');

            /*//Relaciones

            $table->foreign('codigo_producto')->references('codigo')->on('ICO_productos');
            $table->foreign('id_detalle_venta_producto')->references('id')->on('detalles_ventas_productos');*/
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produccion');
    }
};
