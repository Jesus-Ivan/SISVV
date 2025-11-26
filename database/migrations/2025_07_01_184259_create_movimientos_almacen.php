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
        Schema::create('movimientos_almacen', function (Blueprint $table) {
            $table->id();
            $table->integer('folio_entrada')->nullable();
            $table->integer('folio_traspaso')->nullable();
            $table->integer('folio_inventario')->nullable();
            $table->integer('corte_caja')->nullable();
            $table->integer('folio_transformacion')->nullable();
            $table->string('clave_concepto', 255);
            $table->integer('clave_insumo')->nullable();
            $table->integer('clave_presentacion')->nullable();
            $table->string('descripcion', 255);
            $table->string('clave_bodega', 255);
            $table->decimal('cantidad_presentacion', 13, 3)->nullable();
            $table->decimal('rendimiento', 13, 3)->nullable();
            $table->decimal('cantidad_insumo', 13, 3);
            $table->decimal('costo', 10, 2);
            $table->integer('iva');
            $table->decimal('costo_con_impuesto', 10, 2);
            $table->decimal('importe', 10, 2);
            $table->dateTime('fecha_existencias');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_almacen');
    }
};
