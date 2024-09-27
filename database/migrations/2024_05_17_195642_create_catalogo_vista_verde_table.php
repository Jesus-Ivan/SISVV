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
        Schema::create('catalogo_vista_verde', function (Blueprint $table) {
            $table->integer('codigo')->autoIncrement()->unsigned();
            $table->string('nombre', 100);
            $table->string('descripcion', 250)->nullable();
            $table->integer('id_familia')->nullable();
            $table->integer('id_categoria')->nullable();
            $table->integer('id_unidad')->nullable();
            $table->integer('id_proveedor')->nullable();
            $table->integer('stock_amc')->default(0)->nullable();
            $table->decimal('st_min_amc', total:10, places:3)->nullable();
            $table->decimal('st_max_amc', total:10, places:3)->nullable();
            $table->integer('stock_bar')->default(0)->nullable();
            $table->decimal('st_min_bar', total:10, places:3)->nullable();
            $table->decimal('st_max_bar', total:10, places:3)->nullable();
            $table->integer('stock_barra')->default(0)->nullable();
            $table->decimal('st_min_barra', total:10, places:3)->nullable();
            $table->decimal('st_max_barra', total:10, places:3)->nullable();
            $table->integer('stock_caddie')->default(0)->nullable();
            $table->decimal('st_min_caddie', total:10, places:3)->nullable();
            $table->decimal('st_max_cadiie', total:10, places:3)->nullable();
            $table->integer('stock_caf')->default(0)->nullable();
            $table->decimal('st_min_caf', total:10, places:3)->nullable();
            $table->decimal('st_max_caf', total:10, places:3)->nullable();
            $table->decimal('costo_unitario', total:10, places:2)->nullable();
            $table->decimal('costo_empleado', total:10, places:2)->nullable();
            $table->boolean('estado')->default(1);
            $table->string('tipo', 80)->nullable();
            $table->dateTime('ultima_compra')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalogo_vista_verde');
    }
};
