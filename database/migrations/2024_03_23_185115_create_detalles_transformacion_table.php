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
            $table->integer('id')->autoIncrement()->unsigned();
            $table->integer('folio_transformacion')->unsigned();
            $table->integer('clave_insumo_elaborado');
            $table->decimal('cantidad', 11, 3);
            $table->decimal('rendimiento', 11, 3);
            $table->decimal('total_elaborado', 11, 3);
            $table->integer('clave_insumo_receta');
            $table->decimal('cantidad_insumo', 11, 3);
            $table->decimal('cantidad_con_merma', 11, 3);
            $table->decimal('total_sin_merma', 11, 3);
            $table->decimal('merma', 11, 3);
            $table->decimal('total_con_merma', 11, 3);


            /*//Relaciones

            $table->foreign('folio_transformaciones')->references('folio')->on('transformaciones');
            $table->foreign('codigo_materia_prima')->references( 'id' )->on('ICO_materia_prima');*/
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
