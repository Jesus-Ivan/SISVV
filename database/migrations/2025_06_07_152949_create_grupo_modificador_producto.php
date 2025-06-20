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
        Schema::create('grupo_modificador_producto', function (Blueprint $table) {
            $table->id();
            $table->integer('id_grupo');
            $table->integer('clave_producto');
            $table->integer('modif_incluidos');
            $table->integer('modif_maximos');
            $table->boolean('forzar_captura');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grupo_modificador_producto');
    }
};
