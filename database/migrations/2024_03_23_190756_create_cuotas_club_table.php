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
        Schema::create('cuotas_club', function (Blueprint $table) {
            $table->integer('id')->primary()->unsigned();
            $table->integer('id_socio');
            $table->string('tipo_cuota', 80);
            $table->string('descripcion', 255);
            $table->date('fecha_mensualidad');
            $table->decimal('monto', 10, 2);

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
