<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subgrupos extends Model
{
    use HasFactory;

    //Nombre de la tabla
    protected $table = 'subgrupos';
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['id'];
    //Nombre de la llave primaria
    protected $primaryKey = 'id';

}
