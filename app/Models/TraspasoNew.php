<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TraspasoNew extends Model
{
    use HasFactory;
    //Nombre de tabla
    protected $table = 'traspasos_new';
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['folio'];
    //Clave primaria
    protected $primaryKey = 'folio';

    public function origen(): BelongsTo
    {
        return $this->belongsTo(Bodega::class, 'clave_origen', 'clave')
            ->withDefault([
                'clave' => 'N/A',
                'descripcion' => 'N/A',
            ]);
    }

    public function destino(): BelongsTo
    {
        return $this->belongsTo(Bodega::class, 'clave_destino', 'clave')
            ->withDefault([
                'clave' => 'N/A',
                'descripcion' => 'N/A',
            ]);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleTraspasoNew::class, 'folio_traspaso' , 'folio');
    }
}
