<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoMerma extends Model
{
    use HasFactory;
    //Nombre de tabla
    protected $table = 'tipos_mermas';
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['id'];
    //Clave primaria
    protected $primaryKey = 'id';
}
