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
        Schema::create('detalle_traspaso_new', function (Blueprint $table) {
            $table->id();
            $table->integer('folio_traspaso');
            $table->integer('clave_presentacion')->nullable();
            $table->integer('clave_insumo')->nullable();
            $table->string('descripcion', 255);
            $table->decimal('cantidad', 11, 3);
            $table->decimal('rendimiento', 11, 3);
            $table->decimal('cantidad_insumo', 11, 3);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_traspaso_new');
    }
};
