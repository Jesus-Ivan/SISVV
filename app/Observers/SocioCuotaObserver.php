<?php

namespace App\Observers;

use App\Models\Socio;
use App\Models\SocioCuota;

class SocioCuotaObserver
{
    // Resincroniza la fila legacy de socios_membresias cuando cambia cualquier socios_cuotas del socio
    // Cubre alta, edicion y cambio de estado (cancelacion, anualidad, etc.)
    public function saved(SocioCuota $socioCuota): void
    {
        $this->resincronizarLegacy($socioCuota->id_socio);
    }

    // Tambien cubre la eliminacion fisica de una cuota (caso del socio que pierde todas)
    public function deleted(SocioCuota $socioCuota): void
    {
        $this->resincronizarLegacy($socioCuota->id_socio);
    }

    private function resincronizarLegacy(int $idSocio): void
    {
        Socio::find($idSocio)?->sincronizarMembresiaLegacy();
    }
}
