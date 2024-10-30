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
        Schema::create('correcciones_ventas', function (Blueprint $table) {
            $table->id();
            $table->string('user_name',200);
            $table->integer('folio_venta');
            $table->string('tipo_venta',200);
            $table->string('solicitante_name',255);
            $table->integer('id_motivo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversiones_cortesias');
    }
};
