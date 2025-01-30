<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MermaGeneral extends Model
{
    use HasFactory;
    //Nombre de tabla
    protected $table = 'mermas_generales';
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['folio'];
    //Clave primaria
    protected $primaryKey = 'folio';

    public function bodega(): BelongsTo
    {
        return $this->belongsTo(Bodega::class, 'clave_bodega_origen', 'clave');
    }

    public function unidad(): BelongsTo
    {
        return $this->belongsTo(Unidad::class, 'id_unidad', 'id');
    }

    public function tipo():BelongsTo
    {
        return $this->belongsTo(TipoMerma::class,'id_tipo_merma','id');
    }
    
}
