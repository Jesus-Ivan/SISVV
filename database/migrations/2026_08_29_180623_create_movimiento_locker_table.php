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
        Schema::create('movimiento_locker', function (Blueprint $table) {
            $table->id('id_movimiento');

            $table->unsignedBigInteger('id_locker');
            $table->foreign('id_locker')
                ->references('id_locker')
                ->on('locker')
                ->onDelete('cascade');

            $table->unsignedInteger('id_socio')->nullable();
            $table->foreign('id_socio')
                ->references('id')
                ->on('socios')
                ->onDelete('set null');
            $table->string('nombre', 255)->nullable();

            $table->enum('tipo_movimiento', [
                'ASIGNACION',
                'BAJA',
                'ASIGNACION_TRANSFERENCIA',
                'BAJA_TRANSFERENCIA'
            ]);

            $table->dateTime('fecha_movimiento')->useCurrent();
            $table->string('usuario_sistema', 50);
            $table->text('observaciones')->nullable();
            $table->timestamps();

            // Índice para agilizar reportes de auditoría por locker
            $table->index('id_locker');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimiento_locker');
    }
};
