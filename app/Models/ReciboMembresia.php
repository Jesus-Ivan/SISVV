<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReciboMembresia extends Model
{
    use HasFactory;
    //Nombre de tabla
    protected $table = 'recibo_membresia';
    //Propiedades restringidas para asignacion masiva
    protected $guarded = [''];
    //Clave primaria
    protected $primaryKey = 'folio_recibo';
}
