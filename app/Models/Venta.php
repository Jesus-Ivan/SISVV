<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venta extends Model
{
    use HasFactory;
    //Nombre de tabla
    protected $table = 'ventas';
    //Desactivar los timestamps para este modelo
    public $timestamps = false;
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['folio'];
    //Clave primaria
    protected $primaryKey = 'folio';

    public function detallesVentasPago(): HasMany
    {
        return $this->hasMany(DetallesVentaPago::class, 'folio_venta');
    }

    public function detallesProductos(): HasMany
    {
        return $this->hasMany(DetallesVentaProducto::class, 'folio_venta');
    }

    public function correcciones(): HasMany
    {
        return $this->hasMany(CorreccionVenta::class, 'folio_venta');
    }

    public function caja(): BelongsTo
    {
        return $this->belongsTo(Caja::class, 'corte_caja');
    }

    public function puntoVenta(): BelongsTo
    {
        return $this->belongsTo(PuntoVenta::class, 'clave_punto_venta');
    }
}
