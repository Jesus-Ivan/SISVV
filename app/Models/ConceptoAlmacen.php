<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConceptoAlmacen extends Model
{
    use HasFactory;
    //Nombre de la tabla
    protected $table = 'conceptos_almacen';
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['clave'];
    //Clave primaria
    protected $primaryKey = 'clave';
    //Desactivar el autoincremento
    public $incrementing = false;
}
