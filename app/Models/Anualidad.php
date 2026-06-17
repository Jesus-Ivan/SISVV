<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anualidad extends Model
{
    use HasFactory;
    //Nombre de tabla
    protected $table = 'anualidades';
    //Propiedades restringidas para asignacion masiva
    protected $guarded = ['id'];
    //Clave primaria
    protected $primaryKey = 'id';
    //Conversion de tipos
    protected $casts = [
        'cuotas_fijas_eliminar' => 'array',
        'membresias_cancelar' => 'array',
    ];

    /**
     * Aplica los efectos de iniciar la anualidad sobre las membresías y cargos fijos del socio:
     * - La membresía que entra en anualidad (clave_mem_f) pasa a 'ANU'
     * - Se eliminan las cuotas fijas marcadas (cuotas_fijas_eliminar)
     * - Se cancelan (CAN) las membresías marcadas (membresias_cancelar) y se borran sus cuotas
     *
     * Reusado por el proceso mensual (CargosController::activarAnualidad) y por la activación
     * inmediata al registrar una anualidad ya vigente. No genera las mensualidades del mes.
     */
    public function activar(): void
    {
        $socioMembresia = SocioMembresia::where('id_socio', $this->id_socio)
            ->where('clave_membresia', $this->clave_mem_f)
            ->first();
        if (!$socioMembresia) {
            throw new \Exception("No se encontro registro en la tabla socios_membresias para el socio: " . $this->id_socio);
        }
        $socioMembresia->estado = 'ANU';
        $socioMembresia->save();

        //Eliminamos los cargos fijos marcados al registrar la anualidad
        if ($this->cuotas_fijas_eliminar) {
            SocioCuota::where('id_socio', $this->id_socio)
                ->whereIn('id', $this->cuotas_fijas_eliminar)
                ->delete();
        }

        //Cancelamos (CAN) las membresias marcadas y eliminamos sus cuotas fijas
        if ($this->membresias_cancelar) {
            foreach ($this->membresias_cancelar as $claveCancelar) {
                //Nunca cancelar la membresia que entra en la anualidad
                if ($claveCancelar === $this->clave_mem_f) continue;
                SocioMembresia::where('id_socio', $this->id_socio)
                    ->where('clave_membresia', $claveCancelar)
                    ->update(['estado' => 'CAN']);
                SocioCuota::where('id_socio', $this->id_socio)
                    ->whereHas('cuota', fn($q) => $q->where('clave_membresia', $claveCancelar))
                    ->delete();
            }
        }
    }
}
