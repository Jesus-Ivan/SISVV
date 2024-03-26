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
        Schema::create('IBR_barra', function (Blueprint $table) {
            $table->integer('codigo');
            $table->decimal('stock', total:10, places:3);
            $table->decimal('st_min', total:10, places:3);
            $table->decimal('st_max', total:10, places:3);
            $table->decimal('st_copas', total:10, places:3);

            //Relaciones

            $table->foreign('codigo')->references('codigo')->on('IPA_inventario_principal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('IBR_barra');
    }
};
