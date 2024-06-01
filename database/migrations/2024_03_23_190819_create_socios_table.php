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
        Schema::create('socios', function (Blueprint $table) {
            $table->integer('id')->autoIncrement()->unsigned();
            $table->string('nombre', 80);
            $table->string('apellido_p', 80);
            $table->string('apellido_m', 80);
            $table->string('img_path', 255)->nullable();
            $table->date('fecha_registro');
            $table->string('estado_civil', 20)->nullable();
            $table->string('calle', 50)->nullable();
            $table->string('num_exterior', 5)->nullable();
            $table->string('num_interior', 5)->nullable();
            $table->string('colonia', 30)->nullable();
            $table->string('ciudad', 20)->nullable();
            $table->string('estado', 20)->nullable();
            $table->string('codigo_postal', 30)->nullable();
            $table->string('tel_1', 10)->nullable();
            $table->string('tel_2', 10)->nullable();
            $table->string('correo1', 50)->nullable();
            $table->string('correo2', 50)->nullable();
            $table->string('correo3', 50)->nullable();
            $table->string('correo4', 50)->nullable();
            $table->string('correo5', 50)->nullable();
            $table->string('curp', 18)->unique()->nullable();
            $table->string('rfc', 13)->unique()->nullable();

            //Relaciones

            //$table->foreign('clave_membresia')->references('clave')->on('membresias');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('socios');
    }
};
