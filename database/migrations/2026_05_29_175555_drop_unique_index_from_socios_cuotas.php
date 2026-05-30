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
        Schema::table('socios_cuotas', function (Blueprint $table) {
            // Crear índice simple en id_socio para que la FK pueda usarlo
            // antes de eliminar el índice único compuesto (id_socio, id_cuota)
            $table->index('id_socio', 'socios_cuotas_id_socio_idx');
            $table->dropUnique('socios_cuotas_socio_cuota_unique');
        });
    }

    public function down(): void
    {
        Schema::table('socios_cuotas', function (Blueprint $table) {
            $table->unique(['id_socio', 'id_cuota'], 'socios_cuotas_socio_cuota_unique');
            $table->dropIndex('socios_cuotas_id_socio_idx');
        });
    }
};
