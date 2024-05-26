<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tipos extends Model
{
    use HasFactory;
    //Nombre de la tabla
    protected $table = 'tipos';
    //Desactivar los timestamps para este modelo
    public $timestamps = false;
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['clave'];
    //Clave primaria
    protected $primaryKey = 'clave';
    //Desactivamos el auto incremento
    public $incrementing = false;
}
