<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetallesCaja extends Model
{
    use HasFactory;
    //Nombre de tabla
    protected $table = 'detalles_caja';
    //Desactivar los timestamps para este modelo
    public $timestamps = false;
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['id'];
    //Clave primaria
    protected $primaryKey = 'id';

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class, 'folio_venta', 'folio');
    }
}
