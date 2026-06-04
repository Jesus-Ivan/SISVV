<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Homologar tipos con las tablas referenciadas (socios y cuotas usan int unsigned).
        // Se usa SQL crudo en lugar de ->change() para no depender de doctrine/dbal (no instalado).
        DB::statement("ALTER TABLE socios_cuotas MODIFY id_socio INT UNSIGNED NOT NULL");
        DB::statement("ALTER TABLE socios_cuotas MODIFY id_cuota INT UNSIGNED NULL");

        Schema::table("socios_cuotas", function (Blueprint $table) {
            // Columna para precio especial por socio; null = usa tarifa base del catalogo (RF 2.3)
            $table->decimal("monto_personalizado", 10, 2)->nullable()->after("id_cuota");

            // Llaves foraneas formales (Fase 1.1)
            $table->foreign("id_socio")->references("id")->on("socios")->cascadeOnDelete();
            $table->foreign("id_cuota")->references("id")->on("cuotas")->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table("socios_cuotas", function (Blueprint $table) {
            $table->dropForeign(["id_socio"]);
            $table->dropForeign(["id_cuota"]);
            $table->dropColumn("monto_personalizado");
        });

        // Revertir tipos a su definición original (signed) sin depender de doctrine/dbal
        DB::statement("ALTER TABLE socios_cuotas MODIFY id_socio INT NOT NULL");
        DB::statement("ALTER TABLE socios_cuotas MODIFY id_cuota INT NULL");
    }
};
