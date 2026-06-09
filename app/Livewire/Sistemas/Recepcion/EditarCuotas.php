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

    public function limpiarTexto(int $index): void
    {
        $this->cuotas[$index]['texto_concepto'] = null;
        $this->cuotas[$index]['posicion_texto'] = 'izquierda';
    }

    public function guardar(): void
    {
        $this->validate([
            'cuotas.*.monto_personalizado' => 'nullable|numeric|min:0|max:99999999.99',
            'cuotas.*.texto_concepto'      => 'nullable|string|max:100',
            'cuotas.*.posicion_texto'      => 'nullable|in:izquierda,derecha',
        ], [
            'cuotas.*.monto_personalizado.numeric' => 'El precio personalizado debe ser un número.',
            'cuotas.*.monto_personalizado.min'     => 'El precio personalizado no puede ser negativo.',
            'cuotas.*.monto_personalizado.max'     => 'El precio personalizado no puede exceder $99,999,999.99.',
            'cuotas.*.texto_concepto.max'          => 'El texto no puede superar 100 caracteres.',
        ]);

        try {
            DB::transaction(function () {
                foreach ($this->cuotas as $cuota) {
                    $texto = ($cuota['texto_concepto'] ?? '') !== '' ? $cuota['texto_concepto'] : null;
                    SocioCuota::where('id', $cuota['id'])->update([
                        'monto_personalizado' => ($cuota['monto_personalizado'] ?? '') !== '' ? $cuota['monto_personalizado'] : null,
                        'texto_concepto'      => $texto,
                        'posicion_texto'      => $texto ? ($cuota['posicion_texto'] ?? 'izquierda') : null,
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
                'texto_concepto'      => $sc->texto_concepto,
                'posicion_texto'      => $sc->posicion_texto ?? 'izquierda',
            ])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.sistemas.recepcion.editar-cuotas');
    }
}
