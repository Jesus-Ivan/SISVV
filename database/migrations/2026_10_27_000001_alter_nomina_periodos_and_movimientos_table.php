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
        // 1. Ampliar ENUM de 'estado' en nomina_periodos
        DB::statement("ALTER TABLE `nomina_periodos` MODIFY COLUMN `estado` ENUM(
            'BORRADOR',
            'EN_PROCESO',
            'APROBADO',
            'DISPERSADO',
            'PAGADO',
            'CERRADO'
        ) NOT NULL DEFAULT 'BORRADOR'");

        // 2. Agregar 'folio' y 'consecutivo_empleado' en nomina_movimientos_cuenta
        Schema::table('nomina_movimientos_cuenta', function (Blueprint $table) {
            $table->string('folio', 50)->nullable()->after('id_periodo')->index();
            $table->unsignedInteger('consecutivo_empleado')->nullable()->after('folio');
        });

        // 3. Backfill para movimientos existentes
        $movimientos = DB::table('nomina_movimientos_cuenta')->orderBy('id', 'asc')->get();
        $consecutivosPorEmpleado = [];

        foreach ($movimientos as $mov) {
            $idEmp = $mov->id_empleado;
            if (!isset($consecutivosPorEmpleado[$idEmp])) {
                $consecutivosPorEmpleado[$idEmp] = 1;
            } else {
                $consecutivosPorEmpleado[$idEmp]++;
            }

            $consecutivo = $consecutivosPorEmpleado[$idEmp];
            $prefix = 'MOV';
            if ($mov->tipo_movimiento === 'ADELANTO_EFECTIVO') {
                $prefix = 'VAL';
            } elseif ($mov->tipo_movimiento === 'ABONO_DIRECTO') {
                $prefix = 'ABO';
            } elseif ($mov->tipo_movimiento === 'DESCUENTO_NOMINA') {
                $prefix = 'DSC';
            } elseif ($mov->tipo_movimiento === 'AJUSTE') {
                $prefix = 'AJU';
            }

            $folio = sprintf('%s-%04d-%04d', $prefix, $idEmp, $consecutivo);

            DB::table('nomina_movimientos_cuenta')->where('id', $mov->id)->update([
                'folio' => $folio,
                'consecutivo_empleado' => $consecutivo,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nomina_movimientos_cuenta', function (Blueprint $table) {
            $table->dropIndex(['folio']);
            $table->dropColumn(['folio', 'consecutivo_empleado']);
        });

        DB::statement("ALTER TABLE `nomina_periodos` MODIFY COLUMN `estado` ENUM(
            'BORRADOR',
            'APROBADO',
            'PAGADO',
            'CERRADO'
        ) NOT NULL DEFAULT 'BORRADOR'");
    }
};

