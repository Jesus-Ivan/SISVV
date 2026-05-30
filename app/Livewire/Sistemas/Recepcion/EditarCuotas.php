<?php

namespace App\Livewire\Sistemas\Recepcion;

use App\Models\Socio;
use App\Models\SocioCuota;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Locked;
use Livewire\Component;

class EditarCuotas extends Component
{
    #[Locked]
    public Socio $socio;

    public array $cuotas = [];

    public function mount(Socio $socio): void
    {
        $this->socio = $socio->load('cuotasMembresia.cuota');
        $this->cargarCuotas();
    }

    public function limpiar(int $index): void
    {
        $this->cuotas[$index]['monto_personalizado'] = null;
    }

    public function guardar(): void
    {
        $this->validate([
            'cuotas.*.monto_personalizado' => 'nullable|numeric|min:0|max:99999999.99',
        ], [
            'cuotas.*.monto_personalizado.numeric' => 'El precio personalizado debe ser un número.',
            'cuotas.*.monto_personalizado.min'     => 'El precio personalizado no puede ser negativo.',
            'cuotas.*.monto_personalizado.max'     => 'El precio personalizado no puede exceder $99,999,999.99.',
        ]);

        try {
            DB::transaction(function () {
                foreach ($this->cuotas as $cuota) {
                    SocioCuota::where('id', $cuota['id'])->update([
                        'monto_personalizado' => $cuota['monto_personalizado'] !== '' ? $cuota['monto_personalizado'] : null,
                    ]);
                }
            });

            $this->cargarCuotas();
            session()->flash('success', 'Cuotas actualizadas correctamente.');
        } catch (\Throwable $e) {
            session()->flash('fail', 'Ocurrió un error al guardar: ' . $e->getMessage());
        }
    }

    private function cargarCuotas(): void
    {
        $this->cuotas = $this->socio
            ->socioCuotas()
            ->with('cuota')
            ->get()
            ->map(fn($sc) => [
                'id'                  => $sc->id,
                'descripcion'         => $sc->cuota->descripcion,
                'tipo'                => $sc->cuota->tipo,
                'clave_membresia'     => $sc->cuota->clave_membresia,
                'monto_base'          => $sc->cuota->monto,
                'monto_personalizado' => $sc->monto_personalizado,
            ])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.sistemas.recepcion.editar-cuotas');
    }
}
