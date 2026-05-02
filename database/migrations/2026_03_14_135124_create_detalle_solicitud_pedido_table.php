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
        Schema::create('detalle_solicitud_pedido', function (Blueprint $table) {
            $table->id();
            $table->integer('folio_pedido');
            $table->integer('clave_insumo');
            $table->string('descripcion', 255);
            $table->decimal('existencias', 10, 2);
            $table->decimal('cantidad_insumo', 10, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_solicitud_pedido');
    }
};
