<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodoNomina extends Model
{
    use HasFactory;
    //Nombre de la tabla
    protected $table = 'periodos_nomina';
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['referencia'];
    //Clave primaria
    protected $primaryKey = 'referencia';
}
