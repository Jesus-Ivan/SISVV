<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeriodoComprobaciones extends Model
{
    use HasFactory;
    //Nombre de la tabla
    protected $table = 'periodo_comprobaciones';
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['folio'];
    //Clave primaria
    protected $primaryKey = 'folio';

    public function detalles()
    {
        return $this->hasMany(DetallesComprobaciones::class, 'folio_periodo', 'folio');
    }
}
