<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            CREATE VIEW vista_lockers_socios AS
            SELECT 
                l.id_locker,
                l.numero,
                l.seccion,
                l.estado_actual,
                l.id_socio_actual,
                CONCAT_WS(' ',s.nombre, s.apellido_p, s.apellido_m) as socio_nombre ,
                s.deleted_at AS socio_baja,
                l.id_integrante_actual,
                CONCAT_WS(' ',i.nombre_integrante, i.apellido_p_integrante, i.apellido_m_integrante) as integrante_nombre,
                l.observaciones
            FROM locker l
            LEFT JOIN socios s ON l.id_socio_actual = s.id
            LEFT JOIN integrantes_socios i ON l.id_integrante_actual = i.id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS vista_lockers_socios");
    }
};
