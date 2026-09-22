<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LockerSocioView extends Model
{
    use HasFactory;
    // 1. Vincular el modelo con la vista SQL
    protected $table = 'vista_lockers_socios';

    // 2. Definir la llave primaria para que Eloquent sepa cómo identificar filas
    protected $primaryKey = 'id_locker';

    // 3. Las vistas SQL no administran timestamps automáticos de Eloquent
    public $timestamps = false;

    // 4. Se recomienda tratar las vistas como objetos de SOLO LECTURA
    public $incrementing = false;
}
