<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presentacion extends Model
{
    use HasFactory;

    //Nombre de tabla
    protected $table = 'presentaciones';
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['clave'];
    //Clave primaria
    protected $primaryKey = 'clave';

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id')->withDefault([
            'nombre' => 'N/A'
        ]);
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupos::class, 'id_grupo', 'id')
            ->withDefault([
                'descripcion' => 'N/A',
                'tipo' => '',
                'clasificacion' =>'',
            ]);
    }
}
