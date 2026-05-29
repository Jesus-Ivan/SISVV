<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Insertar fila en socios_cuotas para socios cuya membresía principal (no CAN)
        // existe en socios_membresias pero no tiene fila correspondiente en socios_cuotas.
        DB::statement("
            INSERT INTO socios_cuotas (id_socio, id_cuota, auto_delete, created_at, updated_at)
            SELECT sm.id_socio, c.id, 1, NOW(), NOW()
            FROM socios_membresias sm
            JOIN socios s ON s.id = sm.id_socio
            JOIN cuotas c ON c.clave_membresia = sm.clave_membresia AND c.tipo = 'MEN'
            WHERE sm.estado != 'CAN'
              AND NOT EXISTS (
                SELECT 1 FROM socios_cuotas sc
                JOIN cuotas c2 ON sc.id_cuota = c2.id
                WHERE sc.id_socio = sm.id_socio
                  AND c2.clave_membresia = sm.clave_membresia
              )
        ");
    }

    public function down(): void
    {
        DB::statement("
            DELETE sc FROM socios_cuotas sc
            JOIN cuotas c ON sc.id_cuota = c.id
            JOIN socios_membresias sm ON sm.id_socio = sc.id_socio
                AND sm.clave_membresia = c.clave_membresia
            WHERE sc.auto_delete = 1
              AND NOT EXISTS (
                SELECT 1 FROM socios_cuotas sc2
                JOIN cuotas c3 ON sc2.id_cuota = c3.id
                WHERE sc2.id_socio = sc.id_socio
                  AND c3.clave_membresia != sm.clave_membresia
              )
        ");
    }
};
