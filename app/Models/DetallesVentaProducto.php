<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetallesVentaProducto extends Model
{
    use HasFactory;
    use SoftDeletes;
    //Nombre de tabla
    protected $table = 'detalles_ventas_productos';
    //Desactivar los timestamps para este modelo
    public $timestamps = false;
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['id'];
    //Clave primaria
    protected $primaryKey = 'id';

    public function catalogoProductos(): BelongsTo
    {
        return $this->belongsTo(CatalogoVistaVerde::class, 'codigo_catalogo')
            ->withDefault([
                'nombre' => 'ERR N/R'
            ]);;
    }

    public function productos(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'clave_producto')
            ->withDefault([
                'descripcion' => 'ERR N/R'
            ]);
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class, 'folio_venta', 'folio');
    }
}
