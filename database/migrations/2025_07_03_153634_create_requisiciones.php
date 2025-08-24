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
        Schema::create('requisiciones', function (Blueprint $table) {
            $table->integer('folio')->autoIncrement()->unsigned();
            $table->integer('id_user');
            $table->string('tipo_orden', 50);
            $table->string('observaciones', 255)->nullable();
            $table->integer('movimientos');
            $table->decimal('subtotal', total: 10, places: 2);
            $table->decimal('iva', total: 10, places: 2);
            $table->decimal('total', total: 10, places: 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisiciones');
    }
};
