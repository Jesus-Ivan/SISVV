<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('nomina_recibos', function (Blueprint $table) {
            if (!Schema::hasColumn('nomina_recibos', 'consecutivo_empleado')) {
                $table->unsignedInteger('consecutivo_empleado')->default(1)->after('id_empleado')->index();
            }
        });

        // Renumerar recibos existentes por empleado ordenados cronológicamente
        $empleadosIds = DB::table('nomina_recibos')->distinct()->pluck('id_empleado');

        foreach ($empleadosIds as $empId) {
            $recibos = DB::table('nomina_recibos')
                ->where('id_empleado', $empId)
                ->orderBy('fecha_inicio', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            $consecutivo = 1;
            foreach ($recibos as $recibo) {
                $nuevoFolio = sprintf('REC-%04d-%04d', $empId, $consecutivo);
                DB::table('nomina_recibos')
                    ->where('id', $recibo->id)
                    ->update([
                        'consecutivo_empleado' => $consecutivo,
                        'folio' => $nuevoFolio,
                    ]);
                $consecutivo++;
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nomina_recibos', function (Blueprint $table) {
            if (Schema::hasColumn('nomina_recibos', 'consecutivo_empleado')) {
                $table->dropColumn('consecutivo_empleado');
            }
        });
    }
};

