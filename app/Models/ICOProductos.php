<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ICOProductos extends Model
{
    use HasFactory;

    //Nombre de la tabla
    protected $table = 'ico_productos';
    //Desactivar los timestamps para este modelo
    public $timestamps = false;
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['codigo'];
    //Clave primaria
    protected $primaryKey = 'codigo';
}
