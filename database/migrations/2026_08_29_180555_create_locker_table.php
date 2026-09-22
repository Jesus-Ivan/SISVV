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
        Schema::create('locker', function (Blueprint $table) {
            $table->id('id_locker');
            $table->unsignedSmallInteger('numero');
            $table->enum('seccion', ['DAMAS', 'CABALLEROS']);
            $table->enum('estado_actual', ['DISPONIBLE', 'OCUPADO', 'EN_MANTENIMIENTO'])->default('DISPONIBLE');

            // Relación con la tabla 'socios'
            $table->unsignedInteger('id_socio_actual')->nullable();
            $table->foreign('id_socio_actual')
                ->references('id')
                ->on('socios')
                ->onDelete('set null');
            // Relación con la tabla 'Integrantes_socios'   
            $table->unsignedInteger('id_integrante_actual')->nullable();
            $table->foreign('id_integrante_actual')
                ->references('id')
                ->on('integrantes_socios')
                ->onDelete('set null');

            $table->string('observaciones', 255)->nullable();

            // Restricción única para evitar números duplicados por sección
            $table->unique(['numero', 'seccion']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locker');
    }
};
