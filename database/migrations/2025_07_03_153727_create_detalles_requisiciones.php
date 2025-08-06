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
        Schema::create('detalles_requisiciones', function (Blueprint $table) {
            $table->id();
            $table->integer('folio_requisicion');
            $table->integer('clave_presentacion');
            $table->string('descripcion', 100);
            $table->integer('id_proveedor');
            $table->decimal('cantidad', total: 10, places: 2);
            $table->decimal('costo_unitario', total: 10, places: 2);
            $table->decimal('iva', total: 10, places: 2);
            $table->decimal('costo_con_impuesto', total: 10, places: 2)->nullable();
            $table->decimal('importe_sin_impuesto', total: 10, places: 2);
            $table->decimal('impuesto', total: 10, places: 2);
            $table->decimal('importe', total: 10, places: 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_requisiciones');
    }
};
