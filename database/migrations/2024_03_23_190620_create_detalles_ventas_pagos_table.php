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
        Schema::create('detalles_ventas_pagos', function (Blueprint $table) {
            $table->integer('id')->primary()->unsigned();
            $table->integer('folio_venta');
            $table->integer('id_socio')->nullable();
            $table->string('nombre', 80);
            $table->decimal('monto', 10, 2);
            $table->smallInteger('id_tipo_pago');

            //Relaciones

            $table->foreign('folio_venta')->references('folio')->on('ventas');
            $table->foreign('id_socio')->references('id')->on('socios');
            $table->foreign('id_tipo_pago')->references('id')->on('tipos_pago');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_ventas_pagos');
    }
};
