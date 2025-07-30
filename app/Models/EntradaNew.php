<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntradaNew extends Model
{
    use HasFactory;
    //Nombre de tabla
    protected $table = 'entradas_new';
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['folio'];
    //Clave primaria
    protected $primaryKey = 'folio';

    public function bodega(): BelongsTo
    {
        return $this->belongsTo(Bodega::class, 'clave_bodega', 'clave')
            ->withDefault([
                'clave' => 'N/A',
                'descripcion' => 'N/A',
            ]);
    }
}
