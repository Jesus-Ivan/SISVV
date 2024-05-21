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
        Schema::create('detalles_recibo', function (Blueprint $table) {
            $table->integer('folio_recibo');
            $table->integer('id_estado_cuenta');
            $table->integer('id_tipo_pago');
            $table->decimal('saldo_anterior', 10, 2);
            $table->decimal('monto_pago', 10, 2);
            $table->decimal('saldo', 10, 2);
            

            /*//Relaciones

            $table->foreign('folio_recibo')->references('folio')->on('recibos');
            $table->foreign('id_estado_cuenta')->references('id') ->on('estados_cuentas');
            $table->foreign('id_tipo_pago')->references( 'id' )->on('tipos_pago');*/
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_recibo');
    }
};
