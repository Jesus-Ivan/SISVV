<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SocioCuota extends Model
{
    use HasFactory;

    protected $table = 'socios_cuotas';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id_socio',
        'id_cuota',
        'monto_personalizado',
        'texto_concepto',
        'posicion_texto',
        'auto_delete',
    ];

    // Monto real a cobrar: precio personalizado si existe, tarifa base del catalogo si no (RF 2.3 / RF 4)
    protected function montoACobrar(): Attribute
    {
        return Attribute::get(fn() => $this->monto_personalizado ?? $this->cuota->monto);
    }

    /**
     * Concatena el texto_concepto de la cuota a una descripción base, según posicion_texto.
     * Usado al generar el concepto del estado de cuenta (carga manual y masiva) para que el
     * texto personalizado se refleje en ambos flujos de forma idéntica.
     */
    public function aplicarTextoConcepto(string $descripcionBase): string
    {
        $texto = $this->texto_concepto;
        if (($texto ?? '') === '') {
            return $descripcionBase;
        }
        return ($this->posicion_texto ?? 'izquierda') === 'derecha'
            ? $descripcionBase . ' ' . $texto
            : $texto . ' ' . $descripcionBase;
    }

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
