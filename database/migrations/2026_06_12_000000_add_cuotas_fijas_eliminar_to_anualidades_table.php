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
        if (!Schema::hasTable('anualidades')) return;

        //Solo se crea si la columna aun no existe (evita fallo si ya fue agregada en produccion)
        if (!Schema::hasColumn('anualidades', 'cuotas_fijas_eliminar')) {
            Schema::table('anualidades', function (Blueprint $table) {
                //Ids de socios_cuotas marcados en pantalla, que se eliminaran al activarse la anualidad
                $table->json('cuotas_fijas_eliminar')->nullable()->after('observaciones');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('anualidades')) return;

        if (Schema::hasColumn('anualidades', 'cuotas_fijas_eliminar')) {
            Schema::table('anualidades', function (Blueprint $table) {
                $table->dropColumn('cuotas_fijas_eliminar');
            });
        }
    }
};
