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
            $table->dateTime('fecha_apertura');
            $table->integer('id_usuario');
            $table->dateTime('fecha_cierre')->nullable();
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
