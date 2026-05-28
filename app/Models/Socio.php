<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\SocioMembresia;
use App\Models\SocioCuota;

class Socio extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'socios';
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';

    // Membresía principal del socio (fuente de verdad: socios_membresias)
    public function socioMembresia(): HasOne
    {
        return $this->hasOne(SocioMembresia::class, 'id_socio');
    }

    // Todas las cuotas asignadas al socio en socios_cuotas
    public function socioCuotas(): HasMany
    {
        return $this->hasMany(SocioCuota::class, 'id_socio');
    }

    // Membresías adicionales (no-principal) del socio en socios_cuotas
    // Excluye cargos fijos (locker, resguardo, etc.) que no tienen clave_membresia
    public function cuotasMembresia(): HasMany
    {
        return $this->hasMany(SocioCuota::class, 'id_socio')
            ->whereHas('cuota', fn($q) => $q->whereNotNull('clave_membresia'));
    }

    public function integrantesSocio(): HasMany
    {
        return $this->hasMany(IntegrantesSocio::class, 'id_socio');
    }

    // Devuelve la membresía adicional con mayor monto base (usada al rotar la principal)
    public function calcularPrincipalPorValor(): ?SocioCuota
    {
        return $this->cuotasMembresia()
            ->with('cuota')
            ->get()
            ->sortByDesc(fn($sc) => $sc->cuota->monto)
            ->first();
    }

    // Bandera para evitar re-entrada del observer al hacer delete interno
    private static bool $sincronizando = false;

    // Red de seguridad: garantiza que socios_membresias tenga principal cuando el observer detecta
    // un cambio en socios_cuotas fuera del flujo de SocioForm (ej. cargos fijos, eliminaciones externas).
    // SocioForm gestiona rotaciones completas; este método solo cubre el caso "sin principal".
    public function sincronizarMembresiaLegacy(): void
    {
        if (self::$sincronizando) return;
        self::$sincronizando = true;

        try {
            // Si ya existe una fila en socios_membresias, SocioForm la gestiona
            if ($this->socioMembresia()->exists()) return;

            // Sin principal: promover el adicional de mayor monto
            $mayor = $this->cuotasMembresia()->with('cuota')->get()
                ->sortByDesc(fn($sc) => $sc->cuota?->monto ?? 0)
                ->first();

            if (!$mayor?->cuota?->clave_membresia) return;

            SocioMembresia::create([
                'id_socio'         => $this->id,
                'clave_membresia'  => $mayor->cuota->clave_membresia,
                'estado'           => 'MEN',
            ]);

            // Quitarlo de socios_cuotas (ya queda registrado como principal)
            $mayor->delete();

        } finally {
            self::$sincronizando = false;
        }
    }
}
