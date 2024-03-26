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
        Schema::create('detalles_compras', function (Blueprint $table) {
            $table->integer('folio_orden');
            $table->integer('codigo_producto');
            $table->string('nombre', 100);
            $table->integer('id_unidad');
            $table->decimal('cantidad', total:10, places:2);
            $table->integer('id_proveedor');
            $table->decimal('importe', total:10, places:2);
            $table->decimal('iva', total:10, places:2);
            $table->decimal('subtotal', total:10, places:2);
            $table->decimal('almacen', total:10, places:3);
            $table->decimal('bar', total:10, places:3);
            $table->decimal('barra', total:10, places:3);
            $table->decimal('caddie', total:10, places:3);
            $table->decimal('cafeteria', total:10, places:3);
            $table->decimal('cocina', total:10, places:3);
            $table->dateTime('ultima_compra');
            $table->boolean('estado');

            /*
            //Relaciones

            $table->foreign('folio_orden')->references('folio')->on('ordenes_compra');
            $table->foreign('codigo_producto')->references('codigo')->on('IPA_inventario_principal');
            $table->foreign('id_unidad')->references('id')->on('unidades');
            $table->foreign('id_proveedor')->references('id')->on('proveedores');*/
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_compras');
    }
};
