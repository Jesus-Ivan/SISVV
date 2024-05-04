<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Caja extends Model
{
    use HasFactory;
    //Nombre de tabla
    protected $table = 'cajas';
    //Desactivar los timestamps para este modelo
    public $timestamps = false;
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['corte'];
    //Clave primaria
    protected $primaryKey = 'corte';

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function puntoVenta(): BelongsTo
    {
        return $this->belongsTo(PuntoVenta::class, 'clave_punto_venta');
    }

    public function venta(): HasMany
    {
        return $this->hasMany(Venta::class);
    }
}
