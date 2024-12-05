<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SocioCuota extends Model
{
    use HasFactory;
    //Nombre de tabla
    protected $table = 'socios_cuotas';
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['id'];
    //Clave primaria
    protected $primaryKey = 'id';

    public function cuota(): HasOne
    {
        return $this->hasOne(Cuota::class, 'id', 'id_cuota')
            ->withDefault([
                'id' => null,
                'descripcion' => 'N/A',
                'monto' => '0',
                'tipo' => 'N/A',
                'clave_membresia' => 'N/A',
            ]);
    }
}
