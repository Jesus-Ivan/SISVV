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
        Schema::create('salidas', function (Blueprint $table) {
            $table->integer('folio')->autoIncrement()->unsigned();
            $table->date('fecha');
            $table->string('user_name', 255)->nullable();
            $table->string('observaciones', 150)->nullable();
            $table->string('clave_origen', 50);
            $table->string('clave_destino', 50);
            $table->decimal('monto', 10, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salidas');
    }
};
