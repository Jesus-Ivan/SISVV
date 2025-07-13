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
            $table->integer('codigo_catalogo');
            $table->string('nombre', 255);
            $table->string('clave_bodega_origen', 50);
            $table->decimal('cantidad', 10, 3);
            $table->integer('id_unidad');
            $table->integer('id_tipo_merma');
            $table->string('usuario', 255);
            $table->string('observaciones', 255)->nullable();
            $table->timestamps();
            /*//Relaciones

            $table->foreign('codigo_insumo')->references('codigo')->on('ICO_insumos');
            $table->foreign('codigo_articulo')->references('codigo')->on('IPA_inventario_principal');*/
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
