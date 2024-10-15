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
        Schema::create('stocks', function (Blueprint $table) {
            $table->integer('id')->autoIncrement()->unsigned();
            $table->integer('codigo_catalogo')->unsigned();
            $table->decimal('stock_alm', 10, 3)->default(0);
            $table->decimal('stock_alm_min', 10, 3)->nullable();
            $table->decimal('stock_alm_max', 10, 3)->nullable();
            $table->decimal('stock_bar', 10, 3)->default(0);
            $table->decimal('stock_bar_min', 10, 3)->nullable();
            $table->decimal('stock_bar_max', 10, 3)->nullable();
            $table->decimal('stock_res', 10, 3)->default(0);
            $table->decimal('stock_res_min', 10, 3)->nullable();
            $table->decimal('stock_res_max', 10, 3)->nullable();
            $table->decimal('stock_cad', 10, 3)->default(0);
            $table->decimal('stock_cad_min', 10, 3)->nullable();
            $table->decimal('stock_cad_max', 10, 3)->nullable();
            $table->decimal('stock_caf', 10, 3)->default(0);
            $table->decimal('stock_caf_min', 10, 3)->nullable();
            $table->decimal('stock_caf_max', 10, 3)->nullable();
            $table->decimal('stock_loc', 10, 3)->nullable();
            $table->decimal('stock_loc_min', 10, 3)->nullable();
            $table->decimal('stock_loc_max', 10, 3)->nullable();
            $table->decimal('stock_lod', 10, 3)->nullable();
            $table->decimal('stock_lod_min', 10, 3)->nullable();
            $table->decimal('stock_lod_max', 10, 3)->nullable();
            $table->decimal('stock_coc', 10, 3)->default(0);
            $table->decimal('stock_coc_min', 10, 3)->nullable();
            $table->decimal('stock_coc_max', 10, 3)->nullable();
            $table->string('tipo', 255);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
