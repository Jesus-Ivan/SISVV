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
        Schema::create('ICB_bebidas', function (Blueprint $table) {
            $table->integer('codigo')->primary()->unsigned();
            $table->string('categoria', 30);
            $table->string('nombre', 100);
            $table->string('presentacion', 10);
            $table->smallInteger('stock');
            $table->decimal('precio_venta', 10, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ICB_bebidas');
    }
};
