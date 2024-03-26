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
        Schema::create('IPA_inventario_principal', function (Blueprint $table) {
            $table->integer('codigo')->primary()->unsigned();
            $table->integer('id_familia');
            $table->integer('id_categoria');
            $table->string('nombre', 100);
            $table->integer('id_unidad');
            $table->integer('id_proveedor');
            $table->json('punto_venta');
            $table->decimal('stock', total:10, places:3);
            $table->decimal('st_evento', total:10, places:3);
            $table->decimal('st_min', total:10, places:3);
            $table->decimal('st_max', total:10, places:3);
            $table->decimal('costo_unitario', total:10, places:2);
            $table->boolean('estado');

            /*// Relaciones

            $table->foreign('id_familia')->references('id')->on('familias');
            $table->foreign('id_categoria')->references('id')->on('categorias');
            $table->foreign('id_unidad')->references('id')->on('unidades');
            $table->foreign('id_proveedor')->references('id')->on('proveedores');*/
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('IPA_inventario_principal');
    }
};
