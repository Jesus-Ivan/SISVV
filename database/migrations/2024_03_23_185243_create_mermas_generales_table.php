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
        Schema::create('mermas_generales', function (Blueprint $table) {
            $table->integer('folio')->primary()->unsigned();
            $table->integer('codigo_insumo')->nullable();
            $table->dateTime('fecha_registro');
            $table->string('origen', 20);
            $table->integer('codigo_articulo')->nullable();
            $table->float('cantidad');
            $table->string('tipo', 20);
            $table->string('usuario', 20);
            $table->string('detalles', 100);

            //Relaciones

            $table->foreign('codigo_insumo')->references('codigo')->on('ICO_insumos');
            $table->foreign('codigo_articulo')->references('codigo')->on('IPA_inventario_principal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mermas_generales');
    }
};
