<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proveedor extends Model
{
    use HasFactory;

    //Nombre de la tabla
    protected $table = 'proveedores';
    //Desactivar los timestamps para este modelo
    public $timestamps = false;
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['id'];

    public function catalogo(): HasMany
    {
        return $this->hasMany(CatalogoVistaVerde::class, 'id_proveedor', 'id');
    }
}
