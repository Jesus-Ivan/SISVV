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
        Schema::create('detalles_entradas', function (Blueprint $table) {
            $table->integer('codigo_producto');
            $table->integer('folio_entrada');
            $table->string('nombre', 100);
            $table->integer('id_proveedor');
            $table->integer('cantidad')->unsigned()->nullable();
            $table->decimal('peso', 10, 3)->unsigned()->nullable();
            $table->decimal('costo_unitario', 10, 2);
            $table->decimal('importe', 10, 2);
            $table->decimal('iva', 10, 2);
            $table->date('fecha_compra');

            /*//Relaciones
            
            $table->foreign('codigo_articulo')->references('codigo')->on('IPA_inventario_principal');
            $table->foreign('folio_entrada')->references('folio ')->on('entradas');*/
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_entradas');
    }
};
