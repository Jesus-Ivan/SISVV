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
        Schema::create('integrantes_socios', function (Blueprint $table) {
            $table->integer('id_socio')->primary()->unsigned();
            $table->string('nombre', 80);
            $table->string('img_path', 255);
            $table->date('fecha_nac');
            $table->string('sexo', 20);
            $table->string('parentesco', 20);

            //Relaciones

            //$table->foreign('id_socio')->references('id')->on('socios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integrantes_socios');
    }
};
