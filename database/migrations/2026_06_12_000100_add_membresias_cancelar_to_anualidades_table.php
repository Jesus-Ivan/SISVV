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
        if (!Schema::hasColumn('anualidades', 'membresias_cancelar')) {
            Schema::table('anualidades', function (Blueprint $table) {
                //Claves de membresia marcadas en pantalla, que se cancelaran (CAN) al activarse la anualidad
                $table->json('membresias_cancelar')->nullable()->after('cuotas_fijas_eliminar');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('anualidades')) return;

        if (Schema::hasColumn('anualidades', 'membresias_cancelar')) {
            Schema::table('anualidades', function (Blueprint $table) {
                $table->dropColumn('membresias_cancelar');
            });
        }
    }
};
