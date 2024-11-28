<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Traspaso extends Model
{
    use HasFactory;
    //Nombre de la tabla
    protected $table = 'traspasos';
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['folio'];
    //Clave primaria
    protected $primaryKey = 'folio';

    //Relacion con las bodegas de origen
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id')
            ->withDefault([
                'name' => 'N/R',
                'email' => 'N/R'
            ]);
    }
}
