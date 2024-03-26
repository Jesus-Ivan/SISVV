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
        Schema::create('detalles_transformacion', function (Blueprint $table) {
            $table->integer('folio_transformaciones');
            $table->integer('codigo_materia_prima');
            $table->float('peso_ocupado');
            $table->smallInteger('cantidad_ocupada');
            $table->float('merma_transformacion');

            //Relaciones

            $table->foreign('folio_transformaciones')->references('folio')->on('transformaciones');
            $table->foreign('codigo_materia_prima')->references( 'id' )->on('ICO_materia_prima');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_transformacion');
    }
};
