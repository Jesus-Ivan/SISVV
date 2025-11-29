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
        Schema::create('recetas', function (Blueprint $table) {
            $table->id();
            $table->integer('clave_producto')->nullable();
            $table->integer('clave_insumo_elaborado')->nullable();
            $table->integer('clave_insumo');
            $table->decimal('cantidad', 11, 3);
            $table->decimal('cantidad_c_merma', 11, 3)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->timestamps();
            $table->softDeletes($column = 'deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recetas');
    }
};
