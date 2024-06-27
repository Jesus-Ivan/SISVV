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
            $table->decimal('incremento_anual', 10, 2);
            $table->decimal('descuento_membresia', 10, 2);
            $table->string('clave_memb_org', 10);
            $table->string('estado_mem_org', 20);
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
