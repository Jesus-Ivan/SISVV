<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetallesSolicitudPedido extends Model
{
    use HasFactory;
    //Nombre de la tabla
    protected $table = 'detalle_solicitud_pedido';
    //Desactivar los timestamps para este modelo
    public $timestamps = false;
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['id'];
    //Clave primaria
    protected $primaryKey = 'id';

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(TraspasoNew::class, 'folio_pedido', 'folio');
    }
}
