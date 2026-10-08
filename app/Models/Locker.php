<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Locker extends Model
{
    use HasFactory;
    //Nombre de la tabla de referencia
    protected $table = 'locker';
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['id_locker'];
    //Clave primaria
    protected $primaryKey = 'id_locker';

    public function socio(): BelongsTo
    {
        return $this->belongsTo(Socio::class, 'id_socio_actual', 'id')->withDefault([
            'nombre' => '',
            'apellido_p' => '',
            'apellido_m' => '',
        ]);
    }
}
