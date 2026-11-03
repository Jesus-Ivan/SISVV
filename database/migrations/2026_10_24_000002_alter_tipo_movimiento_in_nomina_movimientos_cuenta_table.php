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
        DB::statement("ALTER TABLE `nomina_movimientos_cuenta` MODIFY COLUMN `tipo_movimiento` ENUM(
            'ADELANTO_EFECTIVO',
            'ABONO_DIRECTO',
            'DESCUENTO_NOMINA',
            'AJUSTE',
            'ADELANTO',
            'PRESTAMO',
            'PAGO_NOMINA',
            'AGUINALDO',
            'FINIQUITO'
        ) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `nomina_movimientos_cuenta` MODIFY COLUMN `tipo_movimiento` ENUM(
            'ADELANTO',
            'PRESTAMO',
            'PAGO_NOMINA',
            'DESCUENTO_NOMINA',
            'AGUINALDO',
            'FINIQUITO',
            'AJUSTE'
        ) NOT NULL");
    }
};
