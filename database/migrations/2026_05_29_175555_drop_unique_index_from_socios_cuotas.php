<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        });

        // Solo eliminar si existe — en dev fue creado por una versión anterior de Fase 1;
        // en producción nunca existió, por lo que se omite sin error.
        // SHOW INDEX es nativo de MySQL y no depende de doctrine/dbal (no instalado).
        $existe = ! empty(DB::select(
            "SHOW INDEX FROM socios_cuotas WHERE Key_name = 'socios_cuotas_socio_cuota_unique'"
        ));
        if ($existe) {
            Schema::table('socios_cuotas', function (Blueprint $table) {
                $table->dropUnique('socios_cuotas_socio_cuota_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::table('socios_cuotas', function (Blueprint $table) {
            $table->unique(['id_socio', 'id_cuota'], 'socios_cuotas_socio_cuota_unique');
            $table->dropIndex('socios_cuotas_id_socio_idx');
        });
    }
};
