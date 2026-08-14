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
        Schema::table('detalles_ventas_productos', function (Blueprint $table) {
            $table->string('mesero', 100)->nullable()->after('usuario_cancela');
        });

        // Backfill: copiar el mesero de la cabecera de la venta a cada producto
        DB::table('detalles_ventas_productos as dvp')
            ->join('ventas as v', 'v.folio', '=', 'dvp.folio_venta')
            ->whereNull('dvp.mesero')
            ->update(['dvp.mesero' => DB::raw('v.mesero')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detalles_ventas_productos', function (Blueprint $table) {
            $table->dropColumn('mesero');
        });
    }
};