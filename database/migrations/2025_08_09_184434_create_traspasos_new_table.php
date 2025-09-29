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
        Schema::create('traspasos_new', function (Blueprint $table) {
            $table->integer('folio')->autoIncrement()->unsigned();
            $table->integer('folio_requisicion')->nullable();
            $table->integer('id_user');
            $table->string('nombre', 255);
            $table->string('clave_origen', 255);
            $table->string('clave_destino', 255);
            $table->string('observaciones', 255)->nullable();
            $table->dateTime('fecha_existencias');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traspasos_new');
    }
};
