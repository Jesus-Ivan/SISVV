<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recibo extends Model
{
    use HasFactory;
    //Nombre de tabla
    protected $table = 'recibos';
    //Desactivar los timestamps para este modelo
    public $timestamps = false;
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['folio'];
    //Clave primaria
    protected $primaryKey = 'folio';
}
