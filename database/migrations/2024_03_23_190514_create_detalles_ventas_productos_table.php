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
        Schema::create('detalles_ventas_productos', function (Blueprint $table) {
            $table->integer('id')->autoIncrement()->unsigned();
            $table->integer('folio_venta');
            $table->integer('codigo_catalogo')->nullable();
            $table->integer('clave_producto')->nullable();
            $table->string('chunk', 50)->nullable();
            $table->string('nombre', 255)->nullable();
            $table->smallInteger('cantidad');
            $table->decimal('precio', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->string('observaciones', 255)->nullable();
            $table->string('estado', 1)->nullable();
            $table->dateTime('inicio');
            $table->dateTime('terminado')->nullable();
            $table->string('tiempo', 2)->nullable();
            $table->softDeletes();
            $table->integer('id_cancelacion')->nullable();
            $table->string('motivo_cancelacion', 255)->nullable();
            $table->string('usuario_cancela', 255)->nullable();
            /*//Relaciones

            $table->foreign('folio_venta')->references('folio')->on('ventas');
            $table->foreign('codigo_venta_producto')->references('codigo_venta')->on('catalogo_productos');*/
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_ventas_productos');
    }
};
