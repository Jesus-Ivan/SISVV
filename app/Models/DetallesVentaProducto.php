<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetallesVentaProducto extends Model
{
    use HasFactory;
    //Nombre de tabla
    protected $table = 'detalles_ventas_productos';
    //Desactivar los timestamps para este modelo
    public $timestamps = false;
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['id'];
    //Clave primaria
    protected $primaryKey = 'id';

    public function catalogoProductos(): BelongsTo{
        return $this->belongsTo(CatalogoProducto::class, 'codigo_venta_producto');
    }
}
