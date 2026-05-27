<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Paso 1: Corregir el estado de filas existentes en socios_cuotas cuya cuota es de
        // tipo INA o ANU. El backfill inicial solo sincronizo tipo MEN, por lo que estas
        // quedaron con estado='MEN' por defecto cuando deberian reflejar el tipo de la cuota
        DB::statement("
            UPDATE socios_cuotas sc
            INNER JOIN cuotas c ON sc.id_cuota = c.id
            SET sc.estado = c.tipo
            WHERE c.tipo IN ('INA', 'ANU')
              AND sc.estado = 'MEN'
        ");

        // Paso 2: Crear filas faltantes en socios_cuotas para socios legacy activos
        // Caso 1: el socio tiene clave + estado con cuota correspondiente en el catalogo
        DB::statement("
            INSERT INTO socios_cuotas (id_socio, id_cuota, monto_personalizado, estado, auto_delete, created_at, updated_at)
            SELECT sm.id_socio, c.id, NULL, sm.estado, 1, NOW(), NOW()
            FROM socios_membresias sm
            INNER JOIN socios s ON s.id = sm.id_socio AND s.deleted_at IS NULL
            INNER JOIN cuotas c
                ON c.clave_membresia = sm.clave_membresia
                AND c.tipo = sm.estado
            WHERE sm.estado != 'CAN'
              AND NOT EXISTS (
                  SELECT 1 FROM socios_cuotas sc2 WHERE sc2.id_socio = sm.id_socio
              )
        ");

        // Caso 2: el socio no tiene cuota del tipo exacto (ej. CC-P sin tipo ANU)
        // Se usa la cuota MEN como referencia pero se conserva el estado legacy
        DB::statement("
            INSERT INTO socios_cuotas (id_socio, id_cuota, monto_personalizado, estado, auto_delete, created_at, updated_at)
            SELECT sm.id_socio, c.id, NULL, sm.estado, 1, NOW(), NOW()
            FROM socios_membresias sm
            INNER JOIN socios s ON s.id = sm.id_socio AND s.deleted_at IS NULL
            INNER JOIN cuotas c
                ON c.clave_membresia = sm.clave_membresia
                AND c.tipo = 'MEN'
            WHERE sm.estado != 'CAN'
              AND NOT EXISTS (
                  SELECT 1 FROM socios_cuotas sc2 WHERE sc2.id_socio = sm.id_socio
              )
        ");
    }

    public function down(): void
    {
        // No reversible automaticamente: las filas insertadas no llevan marca distintiva
        // y mezclar criterios de eliminacion podria borrar registros legitimos.
        // Para revertir, restaurar desde respaldo de BD previo a esta migracion
    }
};
