<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventario extends Model
{
    use HasFactory;
    //Nombre de la tabla de referencia
    protected $table = 'inventarios';
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['folio'];
    //Clave primaria
    protected $primaryKey = 'folio';

    /**
     * Relacion de  N:1 con la tabla 'bodegas'
     */
    public function bodega(): BelongsTo
    {
        return $this->belongsTo(Bodega::class, 'clave_bodega', 'clave')->withDefault([
            'Descripcion' => 'N/A'
        ]);
    }
}
