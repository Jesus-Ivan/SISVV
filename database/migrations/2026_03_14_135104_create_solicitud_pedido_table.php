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
        Schema::create('solicitud_pedido', function (Blueprint $table) {
            $table->integer('folio')->unsigned()->autoIncrement();
            $table->integer('id_user');
            $table->string('user_name', 100);
            $table->string('clave_origen', 20);
            $table->datetime('fecha_existencias');
            $table->integer('estado')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_pedido');
    }
};
