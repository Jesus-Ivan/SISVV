<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Requisicion extends Model
{
    use HasFactory;
    //Nombre de la tabla de referencia
    protected $table = 'requisiciones';
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['folio'];
    //Clave primaria
    protected $primaryKey = 'folio';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function detalles()
    {
        return $this->hasMany(DetallesRequisicion::class, 'folio_requisicion', 'folio');
    }
}
