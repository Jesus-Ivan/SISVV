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
        Schema::create('historial_asignacion_locker', function (Blueprint $table) {
            $table->id('id_asignacion');

            $table->unsignedBigInteger('id_locker');
            $table->foreign('id_locker')
                ->references('id_locker')
                ->on('locker')
                ->onDelete('cascade');

            $table->unsignedInteger('id_socio')->nullable();
            $table->foreign('id_socio')
                ->references('id')
                ->on('socios')
                ->onDelete('cascade');
            $table->string('nombre', 255)->nullable();
            $table->string('observaciones', 255)->nullable();

            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin')->nullable();
            $table->timestamps();

            // Índice para optimizar búsquedas por rangos de fecha y socio/locker
            $table->index(['id_locker', 'fecha_inicio', 'fecha_fin'], 'id_locker_fecha_inicio_fecha_fin_index');
            $table->index(['id_socio', 'fecha_inicio', 'fecha_fin'], 'id_socio_fecha_inicio_fecha_fin_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_asignacion_locker');
    }
};
