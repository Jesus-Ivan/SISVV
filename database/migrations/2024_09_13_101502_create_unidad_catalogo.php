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
        Schema::create('unidad_catalogo', function (Blueprint $table) {
            $table->integer('id')->autoIncrement()->unsigned();
            $table->integer('codigo_catalogo');
            $table->integer('id_unidad');
            $table->decimal('costo', total:10, places:2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidad_catalogo');
    }
};
