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
        Schema::create('productos', function (Blueprint $table) {
            $table->integer('clave')->autoIncrement()->unsigned();
            $table->string('descripcion', 255);
            $table->decimal('precio', 10, 2);
            $table->integer('iva');
            $table->decimal('precio_con_impuestos', 10, 2);
            $table->integer('id_grupo');
            $table->integer('id_subgrupo')->nullable();
            $table->integer('estado')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
