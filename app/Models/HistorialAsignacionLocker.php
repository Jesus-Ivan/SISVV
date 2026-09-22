<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialAsignacionLocker extends Model
{
    use HasFactory;
    //Nombre de la tabla de referencia
    protected $table = 'historial_asignacion_locker';
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['id_asignacion'];
    //Clave primaria
    protected $primaryKey = 'id_asignacion';
}
