<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Limpiar duplicados antes de aplicar el indice unico (RF 2.6)
        DB::statement("DELETE sc1 FROM socios_cuotas sc1 INNER JOIN socios_cuotas sc2 ON sc1.id_socio = sc2.id_socio AND sc1.id_cuota = sc2.id_cuota AND sc1.id < sc2.id");

        Schema::table("socios_cuotas", function (Blueprint $table) {
            // Homologar tipos con las tablas referenciadas (socios e cuotas usan integer unsigned)
            $table->unsignedInteger("id_socio")->change();
            $table->unsignedInteger("id_cuota")->nullable()->change();

            // Columna para precio especial por socio; null = usa tarifa base del catalogo (RF 2.3)
            $table->decimal("monto_personalizado", 10, 2)->nullable()->after("id_cuota");

            // Indice unico compuesto para impedir membresías duplicadas (RF 2.6)
            $table->unique(["id_socio", "id_cuota"], "socios_cuotas_socio_cuota_unique");

            // Llaves foraneas formales (Fase 1.1)
            $table->foreign("id_socio")->references("id")->on("socios")->cascadeOnDelete();
            $table->foreign("id_cuota")->references("id")->on("cuotas")->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table("socios_cuotas", function (Blueprint $table) {
            $table->dropUnique("socios_cuotas_socio_cuota_unique");
            $table->dropForeign(["id_socio"]);
            $table->dropForeign(["id_cuota"]);
            $table->dropColumn("monto_personalizado");
            $table->integer("id_socio")->change();
            $table->integer("id_cuota")->nullable()->change();
        });
    }
};
