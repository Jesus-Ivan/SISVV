<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unidad extends Model
{
    use HasFactory;

    //Nombre de la tabla
    protected $table = 'unidades';
    //Desactivar los timestamps para este modelo
    public $timestamps = false;
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['id'];

    public function unidadCatalogo(): HasMany
    {
        return $this->hasMany(UnidadCatalogo::class, 'id_unidad', 'id');
    }
}
