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
            $table->integer('folio')->primary()->unsigned();
            $table->integer('codigo_insumo');
            $table->dateTime('fecha');
            $table->smallInteger('cantidad_ocupada');
            $table->float('peso_resultante');

            //Relaciones

            $table->foreign('codigo_insumo')->references('codigo')->on('ICO_insumos');
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
