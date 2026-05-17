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
        Schema::create('productos_zonas_impresion', function (Blueprint $table) {
            $table->id();
            $table->string('clave_producto', 255);
            $table->string('clave_punto', 255);
            $table->smallInteger('id_zona');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos_zonas_impresion');
    }
};
