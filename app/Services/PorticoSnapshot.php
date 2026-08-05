<?php

namespace App\Services;

use App\Models\IntegrantesSocio;
use App\Models\Membresias;
use App\Models\Socio;
use App\Models\SocioMembresia;
use Illuminate\Support\Collection;

/**
 * Snapshot de socios/membresías/integrantes para PORTICO. La misma consulta
 * la usan el push automático a la API (sync:portico) y la exportación manual
 * de respaldo, para que las dos vías nunca queden desincronizadas entre sí.
 */
class PorticoSnapshot
{
    public static function socios(): Collection
    {
        // Socio excluye borrados por SoftDeletes.
        return Socio::query()
            ->select('id', 'nombre', 'apellido_p', 'apellido_m', 'img_path')
            ->get();
    }

    public static function membresias(): Collection
    {
        return Membresias::query()
            ->select('clave', 'descripcion')
            ->get();
    }

    public static function sociosMembresias(): Collection
    {
        // Solo membresías de socios vigentes. whereExists (en vez de whereIn
        // con todos los ids) para que escale con miles de socios.
        return SocioMembresia::query()
            ->select('id', 'id_socio', 'clave_membresia', 'estado')
            ->whereExists(fn ($q) => $q->from('socios')
                ->whereColumn('socios.id', 'socios_membresias.id_socio')
                ->whereNull('socios.deleted_at'))
            ->get();
    }

    public static function integrantes(): Collection
    {
        // IntegrantesSocio no usa SoftDeletes: filtrar borrados manualmente y
        // solo los que pertenecen a un socio vigente (whereExists, escalable).
        return IntegrantesSocio::query()
            ->select(
                'id',
                'id_socio',
                'nombre_integrante',
                'apellido_p_integrante',
                'apellido_m_integrante',
                'img_path_integrante',
                'parentesco'
            )
            ->whereNull('deleted_at')
            ->whereExists(fn ($q) => $q->from('socios')
                ->whereColumn('socios.id', 'integrantes_socios.id_socio')
                ->whereNull('socios.deleted_at'))
            ->get();
    }
}
