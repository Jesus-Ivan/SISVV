<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetallesVentaPago extends Model
{
    use HasFactory;
    //Nombre de tabla
    protected $table = 'detalles_ventas_pagos';
    //Desactivar los timestamps para este modelo
    public $timestamps = false;
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['id'];
    //Clave primaria
    protected $primaryKey = 'id';

    public function tipoPago(): BelongsTo
    {
        return $this->belongsTo(TipoPago::class, 'id_tipo_pago');
    }
    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class, 'folio_venta');
    }
}
