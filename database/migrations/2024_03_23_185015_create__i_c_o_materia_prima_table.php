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
        Schema::create('ICO_materia_prima', function (Blueprint $table) {
            $table->integer('codigo');
            $table->string('nombre', 50);
            $table->float('stock');
            $table->string('familia', 20);

            //Relaciones

            $table->foreign('codigo')->references('codigo')->on('IPA_inventario_principal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ICO_materia_prima');
    }
};
