<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoPago extends Model
{
    use HasFactory;
    //Nombre de tabla
    protected $table = 'tipos_pago';
    //Desactivar los timestamps para este modelo
    public $timestamps = false;
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['id'];
    //Clave primaria
    protected $primaryKey = 'id';

    public function detallesVentasPago():HasMany{
        return $this->hasMany(DetallesVentaPago::class);
    }
}
