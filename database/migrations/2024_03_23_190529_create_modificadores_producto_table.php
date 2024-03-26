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
        Schema::create('modificadores_producto', function (Blueprint $table) {
            $table->integer('id_venta_producto');
            $table->integer('id_modificador');
            $table->string('descripcion', 80);
            $table->integer('cantidad');
            $table->decimal('precio', 10, 2);
            $table->decimal('monto', 10, 2);

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
