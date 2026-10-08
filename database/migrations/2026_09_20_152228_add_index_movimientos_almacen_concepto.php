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
        Schema::table('movimientos_almacen', function (Blueprint $table) {
            // El orden de las columnas en el índice importa:
            // 1. Igualdad exacta (`clave_bodega`)
            // 2. Rangos de fechas (`fecha_existencias`)
            // 3. Agrupación/Condición IN (`clave_concepto`, `clave_insumo`)
            $table->index(
                ['clave_bodega', 'fecha_existencias', 'clave_concepto', 'clave_insumo'],
                'idx_movimientos_conceptos'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movimientos_almacen', function (Blueprint $table) {$table->dropIndex('idx_movimientos_conceptos');
        });
    }
};
