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
        Schema::create('insumos', function (Blueprint $table) {
            $table->integer('clave')->autoIncrement()->unsigned();
            $table->string('descripcion', 255);
            $table->integer('id_grupo');
            $table->integer('id_unidad');
            $table->decimal('costo', 10, 2)->default(0);
            $table->date('ultima_compra')->nullable();
            $table->integer('iva')->default(0);
            $table->decimal('costo_con_impuesto', 10, 2)->default(0);
            $table->boolean('inventariable')->default(1);
            $table->boolean('elaborado')->default(0);
            $table->decimal('rendimiento_elaborado', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insumos');
    }
};
