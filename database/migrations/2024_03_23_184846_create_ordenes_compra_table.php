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
        Schema::create('ordenes_compra', function (Blueprint $table) {
            $table->integer('folio')->primary()->unsigned();
            $table->dateTime('fecha');
            $table->integer('cantidad');
            $table->decimal('subtotal', total:10, places:2);
            $table->decimal('iva', total:10, places:2);
            $table->decimal('total', total:10, places:2);
            $table->boolean('autorizada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes_compra');
    }
};
