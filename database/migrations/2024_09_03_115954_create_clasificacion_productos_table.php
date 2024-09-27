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
        Schema::create('clasificacion_productos', function (Blueprint $table) {
            $table->integer('id')->autoIncrement()->unsigned();
            $table->string('nombre', 80);
            $table->string('tipo', 50);
            $table->boolean('estado')->default(1);
            $table->timestamps('created_at');
            $table->timestamps('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clasificacion_productos');
    }
};
