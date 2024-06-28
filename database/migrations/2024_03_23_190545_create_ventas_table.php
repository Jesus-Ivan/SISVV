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
        Schema::create('ventas', function (Blueprint $table) {
            $table->integer('folio')->autoIncrement()->unsigned();
            $table->integer('id_socio')->nullable();
            $table->string('nombre', 255);
            $table->dateTime('fecha_apertura');
            $table->dateTime('fecha_cierre')->nullable();
            $table->smallInteger('descuento')->nullable();
            $table->decimal('total', 10, 2);
            $table->integer('corte_caja')->nullable();
            $table->string('clave_punto_venta', 20)->nullable();


            /*//Relaciones

            $table->foreign('id_socio')->references('id')->on('socios');
            $table->foreign('id_tipo_pago')->references('id')->on('tipos_pago');
            $table->foreign('clave_punto_venta')->references('clave')->on('puntos_venta');
            $table->foreign('corte_caja')->references('corte')->on('cajas');*/
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
