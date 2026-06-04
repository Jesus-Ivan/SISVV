<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Inserta en socios_membresias las membresías adicionales que existen en
        // socios_cuotas pero no tienen fila correspondiente en socios_membresias.
        // Guarda NOT EXISTS para evitar duplicados y filtra estados inválidos (EST, etc.).
        DB::statement("
            INSERT INTO socios_membresias (id_socio, clave_membresia, estado)
            SELECT sc.id_socio, c.clave_membresia, c.tipo
            FROM socios_cuotas sc
            JOIN cuotas c ON sc.id_cuota = c.id
            WHERE c.clave_membresia IS NOT NULL
              AND c.clave_membresia != 'N/A'
              AND c.tipo IN ('MEN', 'INA', 'ANU')
              AND NOT EXISTS (
                SELECT 1 FROM socios_membresias sm
                WHERE sm.id_socio = sc.id_socio
                  AND sm.clave_membresia = c.clave_membresia
              )
        ");
    }

    public function down(): void
    {
        // No es reversible automáticamente sin marca distintiva de qué filas se
        // insertaron. Restaurar desde respaldo si es necesario revertir.
    }
};
