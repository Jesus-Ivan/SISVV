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
        Schema::create('detalles_facturas', function (Blueprint $table) {
            $table->integer('id')->autoIncrement()->unsigned();
            $table->integer('folio_factura')->unsigned();
            $table->integer('clave_presentacion');
            $table->decimal('cantidad', 10, 2);
            $table->decimal('costo', 10, 2);
            $table->decimal('iva', 10, 2);
            $table->decimal('impuesto', 10, 2);
            $table->decimal('costo_con_impuesto', 10, 2);
            $table->decimal('importe_sin_impuesto', 10, 2);
            $table->decimal('importe', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_facturas');
    }
};
