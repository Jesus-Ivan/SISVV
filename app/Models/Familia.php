<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Familia extends Model
{
    use HasFactory;

    //Nombre de la tabla
    protected $table = 'familias';
    //Desactivar los timestamps para este modelo
    public $timestamps = false;
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['id'];
}