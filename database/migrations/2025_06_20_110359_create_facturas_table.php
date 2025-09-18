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
        Schema::create('facturas', function (Blueprint $table) {
            $table->integer('folio')->autoIncrement()->unsigned();
            $table->date('fecha_compra');
            $table->date('fecha_vencimiento')->nullable();
            $table->integer('folio_requisicion')->nullable();
            $table->integer('id_proveedor');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('iva', 10, 2);
            $table->decimal('total', 10, 2);
            $table->string('cuenta_contable', 255);
            $table->string('folio_remision', 255)->nullable();
            $table->string('user_name', 255);
            $table->string('observaciones', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
