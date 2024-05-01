<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatalogoProducto extends Model
{
    use HasFactory;
    //Nombre de tabla
    protected $table = 'catalogo_productos';
    //Desactivar los timestamps para este modelo
    public $timestamps = false;
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['codigo_venta'];
    //Clave primaria
    protected $primaryKey = 'codigo_venta';

    public function detallesVentaProductos(): HasMany{
        return $this->hasMany(DetallesVentaProducto::class);
    }
}
