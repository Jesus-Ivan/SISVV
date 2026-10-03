<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleAnualidad extends Model
{
    use HasFactory;
    //Nombre de tabla
    protected $table = 'detalles_anualidades';
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['id'];
    //Clave primaria
    protected $primaryKey = 'id';

    public function anualidad(): BelongsTo
    {
        return $this->belongsTo(Anualidad::class, 'id_anualidad', 'id');
    }

    public function locker():BelongsTo
    {
        return $this->belongsTo(Locker::class, 'id_locker', 'id_locker');
    }
}
