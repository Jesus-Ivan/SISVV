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
        Schema::create('detalles_periodos_nomina', function (Blueprint $table) {
            $table->integer('folio')->unsigned()->autoIncrement();
            $table->integer('referencia_periodo')->unsigned();
            $table->integer('no_empleado')->unsigned();
            $table->string('nombre', 255);
            $table->string('area', 255);
            $table->decimal('nomina_fiscal', 10, 2)->nullable();
            $table->decimal('diferencia_efectivo', 10, 2)->nullable();
            $table->decimal('extras', 10, 2)->nullable();
            $table->decimal('total', 10, 2);
            $table->decimal('descuento', 10, 2)->nullable();
            $table->decimal('infonavit', 10, 2)->nullable();
            $table->decimal('nomina_pagar', 10, 2);
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->string('observaciones', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_periodos_nomina');
    }
};
