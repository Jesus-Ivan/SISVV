<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetallesRequisicion extends Model
{
    use HasFactory;
    use SoftDeletes;
    //Nombre de la tabla de referencia
    protected $table = 'detalles_requisiciones';
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['id'];
    //Clave primaria
    protected $primaryKey = 'id';

}
