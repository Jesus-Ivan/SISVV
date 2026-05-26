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

    // Solo las cuotas de tipo membresia (MEN) para listar membresias contratadas (RF 1 / RF 6)
    public function cuotasMembresia(): HasMany
    {
        return $this->hasMany(SocioCuota::class, 'id_socio')
            ->whereHas('cuota', fn($q) => $q->where('tipo', 'MEN'));
    }

    public function integrantesSocio(): HasMany
    {
        return $this->hasMany(IntegrantesSocio::class, 'id_socio');
    }
}
