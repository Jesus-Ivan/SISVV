<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetallesCompra extends Model
{
    use HasFactory;
    //Nombre de la tabla
    protected $table = 'detalles_compras';
    //Desactivar los timestamps para este modelo
    public $timestamps = false;
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['id'];
    //Clave primaria
    protected $primaryKey = 'id';

    public function proveedores(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id');
    }
}
