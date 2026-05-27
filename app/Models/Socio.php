<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Socio extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    //Nombre de tabla
    protected $table = 'socios';
    //Desactivar los timestamps para este modelo
    public $timestamps = false;
    //Propiedades restringidas para asignacion masiva
    protected $guarded = [];
    //Clave primaria
    protected $primaryKey = 'id';

    // Se conserva para compatibilidad con el codigo existente que usa esta relacion
    public function socioMembresia(): HasOne
    {
        return $this->hasOne(SocioMembresia::class, 'id_socio');
    }

    // Todas las membresias registradas del socio (RF 1)
    public function socioMembresias(): HasMany
    {
        return $this->hasMany(SocioMembresia::class, 'id_socio');
    }

    // Todas las cuotas asignadas al socio en socios_cuotas
    public function socioCuotas(): HasMany
    {
        return $this->hasMany(SocioCuota::class, 'id_socio');
    }

    // Solo las cuotas asociadas a una membresia (tipo MEN/INA/ANU), excluyendo cargos fijos como locker (RF 1 / RF 6)
    // Una cuota es de membresia cuando tiene clave_membresia, independiente de su tipo
    public function cuotasMembresia(): HasMany
    {
        return $this->hasMany(SocioCuota::class, 'id_socio')
            ->whereHas('cuota', fn($q) => $q->whereNotNull('clave_membresia'));
    }

    public function integrantesSocio(): HasMany
    {
        return $this->hasMany(IntegrantesSocio::class, 'id_socio');
    }

    // Determina cual de las membresias del socio debe quedar como principal en la fila legacy
    // Regla: la mas antigua entre las no canceladas. Si todas estan canceladas, la mas antigua a secas.
    public function calcularPrincipalPorAntiguedad(): ?SocioCuota
    {
        $cuotasMembresia = $this->cuotasMembresia()
            ->with('cuota')
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        if ($cuotasMembresia->isEmpty()) {
            return null;
        }

        // Prioridad: mas antigua no cancelada
        $activa = $cuotasMembresia->first(fn($sc) => $sc->estado !== 'CAN');
        if ($activa) {
            return $activa;
        }

        // Fallback: si todas estan canceladas, la mas antigua
        return $cuotasMembresia->first();
    }

    // Mantiene sincronizada la fila legacy de socios_membresias con el estado actual de socios_cuotas
    // Se invoca automaticamente via SocioCuotaObserver tras cada save/delete
    public function sincronizarMembresiaLegacy(): void
    {
        $principal = $this->calcularPrincipalPorAntiguedad();

        if (!$principal) {
            // El socio quedo sin membresias: eliminar la fila legacy si existia
            SocioMembresia::where('id_socio', $this->id)->delete();
            return;
        }

        SocioMembresia::updateOrCreate(
            ['id_socio' => $this->id],
            [
                'clave_membresia' => $principal->cuota->clave_membresia,
                'estado' => $principal->estado,
            ]
        );
    }
}
