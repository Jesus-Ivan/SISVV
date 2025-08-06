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
        Schema::create('entradas_new', function (Blueprint $table) {
            $table->integer('folio')->autoIncrement()->unsigned();
            $table->integer('folio_requisicion')->nullable();
            $table->string('clave_bodega', 50);
            $table->dateTime('fecha_existencias');
            $table->string('observaciones', 255)->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('iva', 10, 2);
            $table->decimal('total', 10, 2);
            $table->integer('id_user');
            $table->string('nombre', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entradas_new');
    }
};
