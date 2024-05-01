<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PuntoVenta extends Model
{
    use HasFactory;
    //Nombre de tabla
    protected $table = 'puntos_venta';
    //Desactivar los timestamps para este modelo
    public $timestamps = false;
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['clave'];
    //Clave primaria
    protected $primaryKey = 'clave';
    //Desactivar el autoincremento
    public $incrementing = false;

    public function caja(): HasMany
    {
        return $this->hasMany(Caja::class);
    }
}
