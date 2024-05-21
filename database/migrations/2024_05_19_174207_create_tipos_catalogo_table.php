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
        Schema::create('tipos_catalogo', function (Blueprint $table) {
            $table->integer('id')->autoIncrement()->unsigned();
            $table->integer('codigo_catalogo');
            $table->string('clave_tipo', 20);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_catalogo');
    }
};
