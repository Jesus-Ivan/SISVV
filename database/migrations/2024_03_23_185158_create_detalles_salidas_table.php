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
        Schema::create('detalles_salidas', function (Blueprint $table) {
            $table->integer('id')->autoIncrement()->unsigned();
            $table->integer('folio_salida');
            $table->integer('codigo_articulo');
            $table->string('nombre', 255);
            $table->integer('stock_origen_cantidad')->nullable();
            $table->decimal('stock_origen_peso', 10, 3)->nullable();
            $table->integer('cantidad_salida')->nullable();
            $table->decimal('peso_salida', 10, 3)->nullable();
            $table->decimal('costo_unitario', 10, 2);
            $table->decimal('monto', 10, 2);

            /*//Referencias

            $table->foreign('folio_salida')->references('folio') ->on('salidas');
            $table->foreign('codigo_articulo')->references('codigo')->on('IPA_inventario_principal');*/
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_salidas');
    }
};
