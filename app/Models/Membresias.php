<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Membresias extends Model
{
    use HasFactory;
    //Nombre de tabla
    protected $table = 'membresias';
    //Desactivar los timestamps para este modelo
    public $timestamps = false;
    //Propiedades restringidas para asignacion masiva
    protected $guarded = [];
    //Clave primaria
    protected $primaryKey = 'clave';
    //Desactivar el autoincremento
    public $incrementing = false;

    public function socio(): HasMany
    {
        return $this->hasMany(Socio::class);
    }
}
