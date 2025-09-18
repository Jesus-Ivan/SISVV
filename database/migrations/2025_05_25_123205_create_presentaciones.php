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
        Schema::create('presentaciones', function (Blueprint $table) {
            $table->integer('clave')->autoIncrement()->unsigned();
            $table->string('descripcion', 255);
            $table->integer('id_grupo');
            $table->decimal('costo', 10, 2)->default(0);
            $table->integer('iva')->default(0);
            $table->decimal('costo_con_impuesto', 10, 2)->default(0);
            $table->integer('clave_insumo_base');
            $table->decimal('rendimiento', 11, 3);
            $table->boolean('redondeo')->default(true);
            $table->decimal('costo_rend', 11, 3);
            $table->decimal('costo_rend_impuesto', 11, 3);
            $table->integer('id_proveedor');
            $table->boolean('estado')->default(1);
            $table->date('ultima_compra')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presentacione');
    }
};
