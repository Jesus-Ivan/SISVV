<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Producto extends Model
{
    use HasFactory;

    //Nombre de tabla
    protected $table = 'productos';
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['clave'];
    //Clave primaria
    protected $primaryKey = 'clave';

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupos::class, 'id_grupo', 'id')
            ->withDefault([
                'descripcion' => 'N/A',
                'tipo' => '',
                'clasificacion' => '',
            ]);
    }

    public function subgrupo(): BelongsTo
    {
        return $this->belongsTo(Subgrupos::class, 'id_subgrupo', 'id')
            ->withDefault([
                'descripcion' => 'N/A',
            ]);
    }
}
