<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitudPedido extends Model
{
    use HasFactory;
    //Nombre de la tabla
    protected $table = 'solicitud_pedido';
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['folio'];
    //Clave primaria
    protected $primaryKey = 'folio';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}
