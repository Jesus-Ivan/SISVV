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
        Schema::create('detalles_entradas', function (Blueprint $table) {
            $table->integer('codigo_articulo');
            $table->string('nombre', 100);
            $table->decimal('cantidad', 10, 2);
            $table->decimal('costo_unitario', 10, 2);
            $table->decimal('importe', 10, 2);
            $table->decimal('iva', 10, 2);
            $table->integer('folio_entrada');

            //Relaciones
            
            $table->foreign('codigo_articulo')->references('codigo')->on('IPA_inventario_principal');
            $table->foreign('folio_entrada')->references('folio ')->on('entradas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_entradas');
    }
};
