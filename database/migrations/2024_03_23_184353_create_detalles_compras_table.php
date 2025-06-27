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
            $table->integer('id')->autoIncrement()->unsigned();
            $table->integer('folio_orden');
            $table->integer('codigo_producto');
            $table->string('nombre', 100);
            $table->integer('id_proveedor');
            $table->integer('id_unidad')->nullable();
            $table->decimal('cantidad', total:10, places:2);
            $table->decimal('costo_unitario', total:10, places:2);
            $table->decimal('iva', total:10, places:2);
            $table->decimal('costo_con_impuesto', total:10, places:2)->nullable();
            $table->decimal('importe', total:10, places:2);
            $table->json('almacen')->nullable();
            $table->json('bar')->nullable();
            $table->json('barra')->nullable();
            $table->json('caddie')->nullable();
            $table->json('cafeteria')->nullable();
            $table->json('cocina')->nullable();
            $table->dateTime('consultado')->nullable();
            $table->date('ultima_compra')->nullable();
            $table->boolean('aplicado')->default(false);
            $table->timestamps();
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
