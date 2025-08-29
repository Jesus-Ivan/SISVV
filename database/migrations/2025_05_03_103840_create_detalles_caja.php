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
        Schema::create('detalles_caja', function (Blueprint $table) {
            $table->id();
            $table->integer('corte_caja')->unsigned();
            $table->integer('folio_venta')->unsigned();
            $table->integer('id_socio')->unsigned()->nullable();
            $table->string('nombre', 255);
            $table->decimal('monto', 10,2);
            $table->decimal('propina', 10,2)->nullable();
            $table->string('tipo_movimiento', 100);
            $table->integer('id_tipo_pago')->unsigned();
            $table->dateTime('fecha_venta')->nullable();
            $table->dateTime('fecha_pago')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_caja');
    }
};
