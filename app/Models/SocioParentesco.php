<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocioParentesco extends Model
{
    use HasFactory;
    //Nombre de la tabla
    protected $table = 'parentesco_socios';
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['parentesco'];
    //Clave primaria
    protected $primaryKey = 'parentesco';
    //Desactivar el autoincremento
    public $incrementing = false;
}
