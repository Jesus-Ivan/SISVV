<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetallesTraspaso extends Model
{
    use HasFactory;
    //Nombre de la tabla
    protected $table = 'detalles_traspasos';
    //Desactivar los timestamps para este modelo
    public $timestamps = false;
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['id'];
    //Clave primaria
    protected $primaryKey = 'id';

    public function traspaso(): BelongsTo
    {
        return $this->belongsTo(Traspaso::class, 'folio_traspaso', 'folio');
    }

    public function bodega_origen(): BelongsTo
    {
        return $this->belongsTo(Bodega::class, 'clave_bodega_origen', 'clave')->withDefault([
            'clave' => null,
            'descripcion' => 'N/R'
        ]);
    }
}
