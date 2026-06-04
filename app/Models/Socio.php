<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\SocioMembresia;
use App\Models\SocioCuota;

class Socio extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'socios';
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';

    // Todas las membresías del socio (nueva relación principal)
    public function socioMembresias(): HasMany
    {
        return $this->hasMany(SocioMembresia::class, 'id_socio');
    }

    // Accessor de compatibilidad — devuelve siempre una fila (nunca null).
    // Prioriza membresías activas sobre CAN para no romper consumidores que
    // acceden a ->estado sin operador null-safe (POS, ReportesController).
    public function socioMembresia(): HasOne
    {
        return $this->hasOne(SocioMembresia::class, 'id_socio')
            ->orderByRaw("FIELD(estado, 'CAN') ASC")
            ->orderBy('id');
    }

    // Todas las cuotas asignadas al socio en socios_cuotas
    public function socioCuotas(): HasMany
    {
        return $this->hasMany(SocioCuota::class, 'id_socio');
    }

    // Membresías adicionales (no-principal) del socio en socios_cuotas
    // Excluye cargos fijos (locker, resguardo, etc.) que no tienen clave_membresia
    public function cuotasMembresia(): HasMany
    {
        return $this->hasMany(SocioCuota::class, 'id_socio')
            ->whereHas('cuota', fn($q) => $q->whereNotNull('clave_membresia'));
    }

    public function integrantesSocio(): HasMany
    {
        return $this->hasMany(IntegrantesSocio::class, 'id_socio');
    }

}
