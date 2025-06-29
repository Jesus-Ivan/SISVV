<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetallesCompra extends Model
{
    use HasFactory;
    use SoftDeletes;
    //Nombre de la tabla
    protected $table = 'detalles_compras';
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['id'];
    //Clave primaria
    protected $primaryKey = 'id';

    public function proveedores(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id');
    }

    public function unidadCatalogo(): HasMany
    {
        return $this->hasMany(UnidadCatalogo::class, 'codigo_catalogo', 'codigo_producto');
    }
}
