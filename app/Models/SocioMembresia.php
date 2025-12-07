<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocioMembresia extends Model
{
    use HasFactory;

    //Nombre de tabla
    protected $table = 'socios_membresias';
    //Desactivar los timestamps para este modelo
    public $timestamps = false;
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['id'];
    //Clave primaria
    protected $primaryKey = 'id';

    public function membresia(): BelongsTo
    {
        return $this->belongsTo(Membresias::class, 'clave_membresia');
    }

    public function socio()
    {
        return $this->belongsTo(Socio::class, 'id_socio', 'id');
    }
}
