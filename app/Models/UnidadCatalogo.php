<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnidadCatalogo extends Model
{
    use HasFactory;
    //Nombre de la tabla
    protected $table = 'unidad_catalogo';
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['id'];

    public function unidad(): BelongsTo
    {
        return $this->belongsTo(Unidad::class, 'id_unidad', 'id');
    }
}
