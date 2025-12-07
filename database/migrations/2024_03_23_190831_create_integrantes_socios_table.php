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
            $table->integer('id')->autoIncrement()->unsigned();
            $table->integer('id_socio')->unsigned();
            $table->string('nombre_integrante', 80)->nullable();
            $table->string('apellido_p_integrante', 80)->nullable();
            $table->string('apellido_m_integrante', 80)->nullable();
            $table->string('img_path_integrante', 255)->nullable();
            $table->date('fecha_nac')->nullable();
            $table->string('parentesco', 20)->nullable();
            $table->string('tel_integrante', 10)->nullable();
            $table->softDeletes();

            //Relaciones

            //$table->foreign('id_socio')->references('id')->on('socios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('integrantes_socios', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
