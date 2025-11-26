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
        Schema::create('transformaciones', function (Blueprint $table) {
            $table->integer('folio')->autoIncrement()->unsigned();
            $table->integer('id_user');
            $table->string('clave_origen', 50);
            $table->string('clave_destino', 50);
            $table->string('observaciones', 255)->nullable();
            $table->dateTime('fecha_existencias');
            $table->timestamps();
            //Relaciones

            //$table->foreign('codigo_insumo')->references('codigo')->on('ICO_insumos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transformaciones');
    }
};
