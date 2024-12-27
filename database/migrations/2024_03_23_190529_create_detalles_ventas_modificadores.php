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
        Schema::create('detalles_ventas_modificadores', function (Blueprint $table) {
            $table->id();
            $table->integer('id_venta_producto');
            $table->integer('codigo_catalogo');
            $table->string('nombre', 255);
            $table->integer('cantidad');
            $table->decimal('precio', 10, 2);
            $table->decimal('subtotal', 10, 2);

            /*//Relaciones

            $table->foreign('id_venta_producto')->references('id')->on('detalles_ventas_productos');
            $table->foreign('id_modificador')->references('codigo')->on('IPA_inventario_principal');*/
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modificadores_producto');
    }
};
