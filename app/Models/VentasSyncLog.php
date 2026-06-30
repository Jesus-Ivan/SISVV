<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VentasSyncLog extends Model
{
    use HasFactory;

    protected $table = 'sync_logs';
    
    // Indicamos la clave primaria personalizada
    protected $primaryKey = 'request_id';

    // Como usamos UUID, no es autoincremental
    public $incrementing = false; 
    
    // Tipo de la clave primaria
    protected $keyType = 'string';

    protected $fillable = [
        'request_id',
        'folio_venta'
    ];

    /**
     * Relación de pertenencia con la Venta.
     */
    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class, 'folio_venta', 'folio');
    }
}

