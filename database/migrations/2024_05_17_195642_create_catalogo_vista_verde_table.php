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
            $table->integer('id_proveedor')->nullable();
            $table->decimal('costo_unitario', total:10, places:2)->nullable();
            $table->decimal('costo_empleado', total:10, places:2)->nullable();
            $table->boolean('estado')->default(1);
            $table->string('clave_dpto', 80);
            $table->string('tipo', 80)->nullable();
            $table->date('ultima_compra')->nullable();
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
