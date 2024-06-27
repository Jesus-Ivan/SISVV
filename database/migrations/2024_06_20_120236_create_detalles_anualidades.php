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
        Schema::create('detalles_anualidades', function (Blueprint $table) {
            $table->integer('id_anualidad');
            $table->integer('id_cuota');
            $table->string('descripcion', 200);
            $table->decimal('monto', 10, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_anualidades');
    }
};
