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
        Schema::create('estados_cuenta', function (Blueprint $table) {
            $table->integer('id')->primary()->unsigned();
            $table->integer('id_cuenta_pago')->nullable();
            $table->integer('folio_evento')->nullable();
            $table->integer('id_cuota')->nullable();
            $table->integer('id_socio')->nullable();
            $table->string('concepto', 100);
            $table->date('fecha_registro');
            $table->decimal('cargos', 10, 2);
            $table->decimal('abonos', 10, 2);
            $table->decimal('saldo', 10, 2);

            //Relaciones

            $table->foreign('id_cuenta_pago')->references('id')->on('detalles_ventas_pagos');
            //$table->foreign('folio_evento')->references('folio')->on('eventos');
            $table->foreign('id_cuota')->references('id')->on('cuotas_club');
            $table->foreign('id_socio')->references('id')->on('socios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estados_cuenta');
    }
};
