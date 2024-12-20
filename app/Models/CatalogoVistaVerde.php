<?php

namespace App\Models;

use App\Livewire\Almacen\Unidades;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatalogoVistaVerde extends Model
{
    use HasFactory;
    //Nombre de la tabla
    protected $table = 'catalogo_vista_verde';
    //Desactivar los timestamps para este modelo
    public $timestamps = false;
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['codigo'];
    //Clave primaria
    protected $primaryKey = 'codigo';

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id')->withDefault([
            'nombre'=> 'N/A'
        ]);
    }
    
    public function familia(): BelongsTo
    {
        return $this->belongsTo(Clasificacion::class, 'id_familia', 'id')->withDefault([
            'nombre'=> 'N/A'
        ]);
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Clasificacion::class, 'id_categoria', 'id')->withDefault([
            'nombre'=> 'N/A'
        ]);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class, 'codigo_catalogo', 'codigo');
    }
}
