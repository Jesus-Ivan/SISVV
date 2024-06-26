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
        Schema::create('detalles_traspasos', function (Blueprint $table) {
            $table->integer('folio_traspaso');
            $table->integer('codigo_articulo');
            $table->string('nombre', 50);
            $table->integer('cantidad');
            $table->float('peso')->nullable();
            $table->decimal('existencia_origen', 10, 3);
            $table->decimal('existencia_destino', 10, 3);

            /*//Relaciones

            $table->foreign('folio_traspaso')->references('folio')->on('traspasos');
            $table->foreign('codigo_articulo')->references('codigo')->on('IPA_inventario_principal');*/
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_traspasos');
    }
};
