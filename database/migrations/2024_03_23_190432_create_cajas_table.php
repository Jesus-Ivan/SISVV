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
        Schema::create('cajas', function (Blueprint $table) {
            $table->integer('corte')->autoIncrement()->unsigned();
            $table->integer('id_usuario')->unsigned();
            $table->dateTime('fecha_apertura');
            $table->dateTime('fecha_cierre')->nullable();
            $table->dateTime('cierre_parcial')->nullable();
            $table->smallInteger('max_eliminaciones')->default(0);
            $table->decimal('cambio_inicial', 10, 2);
            $table->string('clave_punto_venta', 10);


            //Relaciones

            //$table->foreign('id_usuario')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cajas');
    }
};
