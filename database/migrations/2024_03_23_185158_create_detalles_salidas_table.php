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
        Schema::create('detalles_salidas', function (Blueprint $table) {
            $table->integer('folio_salida');
            $table->integer('codigo_articulo');
            $table->integer('cantidad');
            $table->string('salida_de', 20);
            $table->decimal('existencia_origen', 10, 3);

            /*//Referencias

            $table->foreign('folio_salida')->references('folio') ->on('salidas');
            $table->foreign('codigo_articulo')->references('codigo')->on('IPA_inventario_principal');*/
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_salidas');
    }
};
