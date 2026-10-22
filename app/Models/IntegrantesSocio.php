<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IntegrantesSocio extends Model
{
    use HasFactory;
    use SoftDeletes;
    //Nombre de la tabla de referencia
    protected $table = 'integrantes_socios';
    //Desactivar los timestamps para este modelo
    public $timestamps = false;
    //Propiedades restringidas para asignacion masiva
    protected $guarded = [];
}
