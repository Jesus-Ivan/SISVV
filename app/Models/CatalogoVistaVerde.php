<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatalogoVistaVerde extends Model
{
    use HasFactory;
    //Nombre de la tabla
    protected $table = 'catalogo_vista_verde';
    //Desactivar los timestamps para este modelo
    public $timestamps = false;
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['codigo'];
    //Clave primaria
    protected $primaryKey = 'codigo';
}
