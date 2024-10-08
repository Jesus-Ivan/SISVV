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
        Schema::create('anualidades', function (Blueprint $table) {
            $table->id();
            $table->integer('id_socio');
            $table->decimal('membresia_anterior', 10, 2);
            $table->decimal('incremento_anual', 10, 2)->nullable();
            $table->decimal('membresia_nueva', 10, 2);
            $table->decimal('descuento_membresia', 10, 2)->nullable();
            $table->decimal('descuento_extra', 10, 2)->nullable();
            $table->decimal('iva', 10, 2)->nullable();
            $table->string('observaciones', 255)->nullable();
            $table->string('clave_mem_f', 10);
            $table->string('estado_mem_f', 20);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anualidades');
    }
};
