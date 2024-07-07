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
        Schema::create('cambios_turnos', function (Blueprint $table) {
            $table->id();
            $table->integer('id_user');
            $table->string('nombre', 200);
            $table->string('clave_punto_venta', 20);
            $table->string('payload', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cambios_turnos');
    }
};
