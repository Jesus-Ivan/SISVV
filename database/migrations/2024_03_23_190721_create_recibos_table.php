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
        Schema::create('recibos', function (Blueprint $table) {
            $table->integer('folio')->autoIncrement()->unsigned();
            $table->integer('id_socio');
            $table->string('nombre', 80);
            $table->decimal('total', 10, 2);
            $table->integer('corte_caja')->unsigned();
            $table->string('observaciones', 50)->nullable();
            $table->timestamps();

            /*//Relaciones

            $table->foreign('id_socio')->references('id')->on('socios');
            $table->foreign('id_tipo_pago')->references('id')->on('tipos_pago');
            $table->foreign('id_usuario')->references('id')->on('usuarios');*/
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recibos');
    }
};
