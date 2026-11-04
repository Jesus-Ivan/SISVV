<?php

use App\Models\NominaPeriodo;
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
        // 1. Migración de datos existentes:
        // Si algún recibo tenía importes en monto_aguinaldo, monto_vacaciones o monto_prima_vacacional
        // pero sus columnas de dispersión (nomina_fiscal y diferencia_efectivo) estaban en cero,
        // consolidar el dinero en la vía real correspondiente según su método de pago.
        $recibosConMontos = DB::table('nomina_recibos')
            ->where(function ($q) {
                $q->where('monto_aguinaldo', '>', 0)
                  ->orWhere('monto_vacaciones', '>', 0)
                  ->orWhere('monto_prima_vacacional', '>', 0);
            })
            ->get();

        $periodosAfectados = [];

        foreach ($recibosConMontos as $recibo) {
            $periodosAfectados[$recibo->id_periodo] = true;

            $sumaEspecial = (float) $recibo->monto_aguinaldo
                + (float) $recibo->monto_vacaciones
                + (float) $recibo->monto_prima_vacacional;

            // Si ambas vías de dispersión estaban en 0, canalizar la suma
            if ((float) $recibo->nomina_fiscal <= 0.001 && (float) $recibo->diferencia_efectivo <= 0.001) {
                if ($recibo->metodo_pago === 'TRANSFERENCIA') {
                    DB::table('nomina_recibos')->where('id', $recibo->id)->update([
                        'nomina_fiscal' => $sumaEspecial,
                    ]);
                } else {
                    DB::table('nomina_recibos')->where('id', $recibo->id)->update([
                        'diferencia_efectivo' => $sumaEspecial,
                    ]);
                }
            }
        }

        // Recalcular totales para todos los períodos afectados
        foreach (array_keys($periodosAfectados) as $idPeriodo) {
            $periodo = NominaPeriodo::find($idPeriodo);
            if ($periodo) {
                $periodo->recalcularTotales();
            }
        }

        // 2. Eliminar columnas redundantes de la tabla
        Schema::table('nomina_recibos', function (Blueprint $table) {
            $table->dropColumn(['monto_aguinaldo', 'monto_vacaciones', 'monto_prima_vacacional']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nomina_recibos', function (Blueprint $table) {
            $table->decimal('monto_aguinaldo', 10, 2)->default(0)->after('bonos_otros');
            $table->decimal('monto_vacaciones', 10, 2)->default(0)->after('monto_aguinaldo');
            $table->decimal('monto_prima_vacacional', 10, 2)->default(0)->after('monto_vacaciones');
        });
    }
};

