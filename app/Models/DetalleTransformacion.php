<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleTransformacion extends Model
{
    use HasFactory;
    //Nombre de la tabla
    protected $table = 'detalles_transformacion';
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['id'];
    //Clave primaria
    protected $primaryKey = 'id';
    //Desactivar los timestamps para este modelo
    public $timestamps = false;

    /**
     * Obtiene el insumo elaborado
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function insumoElaborado(): BelongsTo
    {
        return $this->belongsTo(Insumo::class, 'clave_insumo_elaborado', 'clave')
            ->withDefault([
                'clave' => 0,
                'descripcion' => 'ERR'
            ]);
    }

    /**
     * Obtiene el insumo utilizado para la receta
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function insumoReceta(): BelongsTo
    {
        return $this->belongsTo(Insumo::class, 'clave_insumo_receta', 'clave');
    }

    /**
     * Obtiene la transformacion
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transformacion(): BelongsTo
    {
        return $this->belongsTo(Transformacion::class, 'folio_transformacion', 'folio');
    }
}
