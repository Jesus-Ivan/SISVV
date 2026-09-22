<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoLocker extends Model
{
    use HasFactory;
    //Nombre de la tabla de referencia
    protected $table = 'movimiento_locker';
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['id_movimiento'];
    //Clave primaria
    protected $primaryKey = 'id_movimiento';


    /**
     * Get the user that owns the MovimientoLocker
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function locker(): BelongsTo
    {
        return $this->belongsTo(Locker::class, 'id_locker', 'id_locker');
    }
}
