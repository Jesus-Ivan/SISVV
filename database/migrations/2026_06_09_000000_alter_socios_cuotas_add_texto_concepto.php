<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('socios_cuotas')) return;

        //Cada columna solo se crea si aun no existe (evita fallo si ya fue agregada en produccion)
        Schema::table('socios_cuotas', function (Blueprint $table) {
            if (!Schema::hasColumn('socios_cuotas', 'texto_concepto')) {
                $table->string('texto_concepto', 100)->nullable()->after('monto_personalizado');
            }
            if (!Schema::hasColumn('socios_cuotas', 'posicion_texto')) {
                $table->enum('posicion_texto', ['izquierda', 'derecha'])->nullable()->after('texto_concepto');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('socios_cuotas')) return;

        Schema::table('socios_cuotas', function (Blueprint $table) {
            if (Schema::hasColumn('socios_cuotas', 'texto_concepto')) {
                $table->dropColumn('texto_concepto');
            }
            if (Schema::hasColumn('socios_cuotas', 'posicion_texto')) {
                $table->dropColumn('posicion_texto');
            }
        });
    }
};
