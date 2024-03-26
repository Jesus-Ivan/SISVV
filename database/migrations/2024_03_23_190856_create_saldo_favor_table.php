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
        Schema::create('saldo_favor', function (Blueprint $table) {
            $table->integer('id')->primary()->unsigned();
            $table->integer('folio_recibo_origen');
            $table->decimal('saldo', 10, 2);
            $table->string('aplicado_a', 80);

            //Relaciones

            $table->foreign('folio_recibo_origen')->references('folio')->on('recibos');
            $table->foreign('aplicado_a')->references('folio')->on('recibos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saldo_favor');
    }
};
