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
        Schema::create('detalle_entrada_new', function (Blueprint $table) {
            $table->id();
            $table->integer('folio_entrada')->unsigned();
            $table->integer('clave_presentacion')->nullable();
            $table->integer('clave_insumo')->nullable();
            $table->string('descripcion', 255);
            $table->integer('id_proveedor');
            $table->decimal('cantidad', 11, 3);
            $table->decimal('costo_unitario', 10, 2);
            $table->decimal('iva', 10, 2);
            $table->decimal('costo_con_impuesto', 10, 2);
            $table->decimal('importe', 10, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_entrada_new');
    }
};
