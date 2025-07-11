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
        Schema::create('detalles_inventario', function (Blueprint $table) {
            $table->id();
            $table->integer('folio_inventario');
            $table->integer('clave_insumo')->nullable();
            $table->integer('clave_presentacion')->nullable();
            $table->string('descripcion', 255);
            $table->decimal('stock_teorico', 13, 3);
            $table->decimal('stock_fisico', 13, 3);
            $table->decimal('diferencia_almacen', 13, 3);
            $table->decimal('diferencia_importe', 13, 3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_inventarios');
    }
};
