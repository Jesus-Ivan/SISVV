<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receta extends Model
{
    use HasFactory;
    use SoftDeletes; //Eliminaciones suaves en el modelo
    
    //Nombre de tabla
    protected $table = 'recetas';
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['id'];
    //Clave primaria
    protected $primaryKey = 'id';

    public function ingrediente(): BelongsTo
    {
        return $this->belongsTo(Insumo::class, 'clave_insumo', 'clave');
    }
}
