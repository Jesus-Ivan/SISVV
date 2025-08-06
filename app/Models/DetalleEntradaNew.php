<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleEntradaNew extends Model
{
    use HasFactory;
    //Nombre de la tabla
    protected $table = 'detalle_entrada_new';
    //Desactivar los timestamps para este modelo
    public $timestamps = false;
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['id'];
    //Clave primaria
    protected $primaryKey = 'id';

    public function entrada(): BelongsTo
    {
        return $this->belongsTo(EntradaNew::class, 'folio_entrada', 'folio');
    }

    public function proveedor():BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id')
        ->withDefault([
            'nombre' => 'N/A',
        ])
        ;
    }
}
