<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table("socios_cuotas", function (Blueprint $table) {
            // Estado individual por membresia / cuota del socio (MEN, INA, ANU, CAN)
            // Permite que un socio tenga una membresia cancelada y otra activa al mismo tiempo
            $table->string("estado", 3)->default("MEN")->after("monto_personalizado");
        });

        // Backfill: copiar el estado actual desde socios_membresias hacia las cuotas de tipo MEN
        // Las cuotas de cargos fijos (locker, resguardo, etc.) conservan el default MEN
        DB::statement("
            UPDATE socios_cuotas sc
            INNER JOIN socios_membresias sm ON sc.id_socio = sm.id_socio
            INNER JOIN cuotas c ON sc.id_cuota = c.id
            SET sc.estado = sm.estado
            WHERE c.tipo = 'MEN'
        ");
    }

    public function down(): void
    {
        Schema::table("socios_cuotas", function (Blueprint $table) {
            $table->dropColumn("estado");
        });
    }
};
