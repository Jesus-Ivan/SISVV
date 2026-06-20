<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Si la tabla no existe, no hay nada que modificar (evita fallo por orden de migraciones)
        if (!Schema::hasTable("socios_cuotas")) return;

        // Columna para precio especial por socio; null = usa tarifa base del catalogo (RF 2.3)
        // Solo si no existe (evita fallo si ya fue agregada en produccion)
        if (!Schema::hasColumn("socios_cuotas", "monto_personalizado")) {
            Schema::table("socios_cuotas", function (Blueprint $table) {
                $table->decimal("monto_personalizado", 10, 2)->nullable()->after("id_cuota");
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable("socios_cuotas")) return;

        if (Schema::hasColumn("socios_cuotas", "monto_personalizado")) {
            Schema::table("socios_cuotas", fn(Blueprint $table) => $table->dropColumn("monto_personalizado"));
        }
    }
};
