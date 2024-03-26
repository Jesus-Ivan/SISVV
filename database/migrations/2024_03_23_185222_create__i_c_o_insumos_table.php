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
        Schema::create('ICO_insumos', function (Blueprint $table) {
            $table->integer('codigo')->primary()->unsigned();
            $table->string('nombre', 50);
            $table->smallInteger('cantidad');
            $table->float('peso');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ICO_insumos');
    }
};
