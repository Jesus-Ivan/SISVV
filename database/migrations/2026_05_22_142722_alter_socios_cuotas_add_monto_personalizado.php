<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Si la tabla no existe, no hay nada que modificar (evita fallo por orden de migraciones)
        if (!Schema::hasTable("socios_cuotas")) return;

        // Homologar tipos con las tablas referenciadas (socios y cuotas usan int unsigned).
        // Se usa SQL crudo en lugar de ->change() para no depender de doctrine/dbal (no instalado).
        // El MODIFY al mismo tipo es idempotente: re-ejecutarlo no causa error.
        DB::statement("ALTER TABLE socios_cuotas MODIFY id_socio INT UNSIGNED NOT NULL");
        DB::statement("ALTER TABLE socios_cuotas MODIFY id_cuota INT UNSIGNED NULL");

        // Columna: solo si no existe (evita fallo si ya fue agregada en produccion)
        if (!Schema::hasColumn("socios_cuotas", "monto_personalizado")) {
            Schema::table("socios_cuotas", function (Blueprint $table) {
                // Columna para precio especial por socio; null = usa tarifa base del catalogo (RF 2.3)
                $table->decimal("monto_personalizado", 10, 2)->nullable()->after("id_cuota");
            });
        }

        // Llaves foraneas: solo si no existen (evita "Duplicate foreign key constraint name")
        if (!$this->tieneForanea("socios_cuotas_id_socio_foreign")) {
            Schema::table("socios_cuotas", function (Blueprint $table) {
                $table->foreign("id_socio")->references("id")->on("socios")->cascadeOnDelete();
            });
        }
        if (!$this->tieneForanea("socios_cuotas_id_cuota_foreign")) {
            Schema::table("socios_cuotas", function (Blueprint $table) {
                $table->foreign("id_cuota")->references("id")->on("cuotas")->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable("socios_cuotas")) return;

        // Cada elemento solo se elimina si existe (evita fallo si ya fue revertido manualmente)
        if ($this->tieneForanea("socios_cuotas_id_socio_foreign")) {
            Schema::table("socios_cuotas", fn(Blueprint $table) => $table->dropForeign(["id_socio"]));
        }
        if ($this->tieneForanea("socios_cuotas_id_cuota_foreign")) {
            Schema::table("socios_cuotas", fn(Blueprint $table) => $table->dropForeign(["id_cuota"]));
        }
        if (Schema::hasColumn("socios_cuotas", "monto_personalizado")) {
            Schema::table("socios_cuotas", fn(Blueprint $table) => $table->dropColumn("monto_personalizado"));
        }

        // Revertir tipos a su definición original (signed) sin depender de doctrine/dbal
        DB::statement("ALTER TABLE socios_cuotas MODIFY id_socio INT NOT NULL");
        DB::statement("ALTER TABLE socios_cuotas MODIFY id_cuota INT NULL");
    }

    // Devuelve true si la llave foranea indicada ya existe en socios_cuotas
    private function tieneForanea(string $nombre): bool
    {
        return !empty(DB::select(
            "SELECT 1 FROM information_schema.TABLE_CONSTRAINTS
             WHERE CONSTRAINT_SCHEMA = DATABASE()
               AND TABLE_NAME = 'socios_cuotas'
               AND CONSTRAINT_NAME = ?
               AND CONSTRAINT_TYPE = 'FOREIGN KEY'",
            [$nombre]
        ));
    }
};
