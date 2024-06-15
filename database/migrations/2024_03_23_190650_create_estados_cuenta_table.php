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
            $table->integer('id')->autoIncrement()->unsigned();
            $table->integer('id_venta_pago')->nullable();
            $table->integer('folio_evento')->nullable();
            $table->integer('id_cuota')->nullable();
            $table->integer('id_socio')->nullable();
            $table->string('concepto', 100);
            $table->date('fecha');
            $table->decimal('cargo', 10, 2)->default(0);
            $table->decimal('abono', 10, 2)->default(0);
            $table->decimal('saldo', 10, 2)->default(0);
            $table->decimal('saldo_favor', 10, 2)->default(0);
            $table->boolean('consumo')->nullable()->default(0);
            $table->string('vista', 50)->nullable()->default('ORD');;
            $table->timestamps();

            /*//Relaciones

            $table->foreign('id_cuenta_pago')->references('id')->on('detalles_ventas_pagos');
            //$table->foreign('folio_evento')->references('folio')->on('eventos');
            $table->foreign('id_cuota')->references('id')->on('cuotas_club');
            $table->foreign('id_socio')->references('id')->on('socios');*/
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
