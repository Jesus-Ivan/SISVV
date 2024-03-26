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
        Schema::create('catalogo_productos', function (Blueprint $table) {
            $table->integer('codigo_venta')->primary()->unsigned();
            $table->integer('codigo_principal')->nullable();
            $table->integer('codigo_recepcion')->nullable();
            $table->integer('codigo_cocina')->nullable();
            $table->integer('codigo_bebida')->nullable();
            $table->string('nombre', 50);
            $table->decimal('precio_venta', 10, 2);
            $table->json('pv_permitidos');

            /*//Relaciones

            $table->foreign('codigo_principal')->references('codigo')->on('IPA_inventario_principal');
            $table->foreign('codigo_recepcion')->references('codigo')->on('IRC_recepcion');
            $table->foreign('codigo_cocina')->references('codigo')->on('ICO_productos');
            $table->foreign('codigo_bebida')->references('codigo')->on('ICB_bebidas');*/
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalogo_productos');
    }
};
