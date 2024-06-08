<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Socio extends Model
{
    use HasFactory;
    //Nombre de tabla
    protected $table = 'socios';
    //Desactivar los timestamps para este modelo
    public $timestamps = false;
    //Propiedades restringidas para asignacion masiva
    protected $guarded = [];
    //Clave primaria
    protected $primaryKey = 'id';

    public function socioMembresia(): HasOne
    {
        return $this->hasOne(SocioMembresia::class, 'id_socio');
    }

    public function integrantesSocio(): HasMany
    {
        return $this->hasMany(IntegrantesSocio::class, 'id_socio');
    }
}
