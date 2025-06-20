<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Modificador extends Model
{
    use HasFactory;

    //Nombre de tabla
    protected $table = 'modificadores';
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['id'];
    //Clave primaria
    protected $primaryKey = 'id';

    public function productoModif(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'clave_modificador', 'clave')
            ->withDefault([
                'descripcion' => 'N/A',
                'precio' => 0
            ]);
    }
}
