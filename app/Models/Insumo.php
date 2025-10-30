<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Insumo extends Model
{
    use HasFactory;

    //Nombre de la tabla de referencia
    protected $table = 'insumos';
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['clave'];
    //Clave primaria
    protected $primaryKey = 'clave';


    /**
     * Define la relación "uno a muchos" con los movimientos de almacén.
     */
    public function movimientosAlmacen(): HasMany
    {
        return $this->hasMany(MovimientosAlmacen::class, 'clave_insumo', 'clave');
    }

    public function unidad(): BelongsTo
    {
        return $this->belongsTo(Unidad::class, 'id_unidad', 'id')
            ->withDefault([
                'descripcion' => 'N/A',
                'estado' => 0
            ]);
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupos::class, 'id_grupo', 'id')
            ->withDefault([
                'descripcion' => 'N/A',
                'tipo' => '',
                'clasificacion' => '',
            ]);
    }

    public function presentaciones(): HasMany
    {
        return $this->hasMany(Presentacion::class, 'clave_insumo_base');
    }
}
