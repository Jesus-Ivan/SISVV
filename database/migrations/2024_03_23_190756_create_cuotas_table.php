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
        Schema::create('cuotas', function (Blueprint $table) {
            $table->integer('id')->autoIncrement()->unsigned();
            $table->string('descripcion', 100);
            $table->decimal('monto', 10, 2);
            $table->string('tipo', 20);
            $table->string('clave_membresia', 10)->nullable();

            //Relaciones
            //$table->foreign('id_socio')->references('id')->on('socios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuotas_club');
    }
};
