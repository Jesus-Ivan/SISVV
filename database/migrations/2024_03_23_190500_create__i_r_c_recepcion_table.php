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
        Schema::create('IRC_recepcion', function (Blueprint $table) {
            $table->integer('codigo')->primary()->unsigned();
            $table->string('nombre', 80);
            $table->string('precio_venta', 80);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('IRC_recepcion');
    }
};
