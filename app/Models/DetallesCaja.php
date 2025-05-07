<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetallesCaja extends Model
{
    use HasFactory;
    //Nombre de tabla
    protected $table = 'detalles_caja';
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['id'];
    //Clave primaria
    protected $primaryKey = 'id';

}
