<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetallesFacturas extends Model
{
    use HasFactory;
    //Nombre de la tabla
    protected $table = 'detalles_facturas';
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['id'];
    //Clave primaria
    protected $primaryKey = 'id';

    public function factura(): BelongsTo
    {
        return $this->belongsTo(Facturas::class, 'folio_factura');
    }

    public function presentacion(): BelongsTo
    {
        return $this->belongsTo(Presentacion::class, 'clave_presentacion', 'clave');
    }
}
