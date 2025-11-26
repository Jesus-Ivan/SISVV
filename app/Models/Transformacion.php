<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transformacion extends Model
{
    use HasFactory;
    //Nombre de la tabla
    protected $table = 'transformaciones';
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['folio'];
    //Clave primaria
    protected $primaryKey = 'folio';

    /**
     * Obtiene la bodega de origen de la transformacion
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function origen(): BelongsTo
    {
        return $this->belongsTo(Bodega::class, 'clave_origen')->withDefault([
            'clave' => 0,
            'descripcion' => 'ERR',
        ]);
    }

    /**
     * Get the user that owns the Transformacion
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function destino(): BelongsTo
    {
        return $this->belongsTo(Bodega::class, 'clave_destino')
            ->withDefault([
                'clave' => 0,
                'descripcion' => 'ERR',
            ]);
    }
}
