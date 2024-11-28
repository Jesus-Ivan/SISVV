<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Salida extends Model
{
    use HasFactory;

    //Nombre de la tabla
    protected $table = 'salidas';
    //Desactivar los timestamps para este modelo
    public $timestamps = false;
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['folio'];
    //Clave primaria
    protected $primaryKey = 'folio';

    public function bodegaOrigen(): BelongsTo
    {
        return $this->belongsTo(Bodega::class, 'clave_origen', 'clave')
            ->withDefault(['descripcion' => 'clave bodega invalida']);
    }

    public function destino(): BelongsTo
    {
        return $this->belongsTo(Bodega::class, 'clave_destino', 'clave')
        ->withDefault(['descripcion' => 'clave bodega invalida']);
    }
}
