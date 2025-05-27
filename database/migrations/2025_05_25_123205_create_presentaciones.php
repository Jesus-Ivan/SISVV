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
            $table->decimal('costo',10,2);
            $table->integer('iva')->nullable();
            $table->integer('clave_insumo_base');
            $table->integer('rendimiento');
            $table->integer('id_proveedor');
            $table->boolean('estado')->default(1);
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
