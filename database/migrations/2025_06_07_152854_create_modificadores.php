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
        Schema::create('modificadores', function (Blueprint $table) {
            $table->id();
            $table->integer('id_grupo');
            $table->integer('clave_producto');
            $table->integer('clave_modificador');
            $table->decimal('precio', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modificadores');
    }
};
