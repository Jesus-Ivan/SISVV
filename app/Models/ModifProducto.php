<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModifProducto extends Model
{
    use HasFactory;
    use SoftDeletes; //Eliminaciones suaves en el modelo

    //Nombre de tabla
    protected $table = 'grupo_modificador_producto';
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['id'];
    //Clave primaria
    protected $primaryKey = 'id';


    public function grupoModif(): BelongsTo
    {
        return $this->belongsTo(GruposModificadores::class, 'id_grupo', 'id')
            ->withDefault([
                'descripcion' => 'N/A',
            ]);
    }
}
