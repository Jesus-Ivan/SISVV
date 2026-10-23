<?php

namespace App\Livewire\Concerns;

use App\Constants\AlmacenConstants;
use App\Libraries\InventarioService;
use App\Models\DetallesSolicitudPedido;
use App\Models\Insumo;
use App\Models\SolicitudPedido;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;

trait ManejaSolicitudInventario
{
    abstract protected function claveBodega(): string;

    public function eliminarArticulo($index)
    {
        unset($this->lista_productos[$index]);
    }

    #[Computed()]
    public function insumos()
    {
        $query = Insumo::query()
            ->where('inventariable', true);

        if ($this->selectedGrupo) {
            $query->where('id_grupo', $this->selectedGrupo);
        }

        if (!empty($this->searchInsumo)) {
            $query->where(function ($q) {
                $q->where('descripcion', 'like', '%' . $this->searchInsumo . '%')
                    ->orWhere('clave', 'like', '%' . $this->searchInsumo . '%');
            });
        }

        return $query->take(20)->get();
    }

    #[Computed()]
    public function grupos()
    {
        return DB::table('grupos')
            ->where('tipo', AlmacenConstants::INSUMOS_KEY)
            ->orderBy('descripcion', 'ASC')
            ->get();
    }

    public function finalizarSeleccion()
    {
        $inventario = new InventarioService();
        $hoy = now();
        $claveBodega = $this->claveBodega();

        foreach (array_filter($this->selectedItems) as $clave => $value) {
            $producto = Insumo::with('unidad')->find($clave);

            if ($producto) {
                $existencias = $inventario->existenciasInsumo(
                    $producto->clave,
                    $hoy->toDateString(),
                    $hoy->toTimeString(),
                    $claveBodega
                );

                $this->lista_productos[] = [
                    'clave' => $producto->clave,
                    'descripcion' => $producto->descripcion,
                    'existencia' => $existencias[0]['existencias_insumo'] ?? 0,
                    'cantidad' => 1,
                    'unidad' => $producto->unidad,
                ];
            }
        }

        $this->reset(['selectedItems', 'searchInsumo', 'selectedGrupo']);
        $this->dispatch('close-modal', name: 'modal-productos');
    }

    public function aplicarSolicitud()
    {
        $user = auth()->user();
        $validated = $this->validate([
            'lista_productos' => 'required|min:1',
        ]);

        try {
            DB::transaction(function () use ($user, $validated) {
                $claveBodega = $this->claveBodega();
                $result = SolicitudPedido::create([
                    'id_user' => $user->id,
                    'user_name' => $user->name,
                    'clave_pv' => $claveBodega,
                    'fecha_existencias' => $this->hoy,
                ]);

                foreach ($validated['lista_productos'] as $row) {
                    DetallesSolicitudPedido::create([
                        'folio_pedido' => $result->folio,
                        'clave_insumo' => $row['clave'],
                        'descripcion' => $row['descripcion'],
                        'existencias' => $row['existencia'],
                        'cantidad_insumo' => $row['cantidad'],
                    ]);
                }
            });

            $this->reset(['selectedItems', 'searchInsumo', 'lista_productos', 'selectedGrupo']);
            session()->flash('success', 'Solicitud registrada exitosamente.');
            $this->dispatch('open-action-message');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $th) {
            session()->flash('fail', $th->getMessage());
        }
    }

    /** Evita cantidades vacías o negativas. */
    public function actualizarCantidad($index)
    {
        if (strlen($this->lista_productos[$index]['cantidad']) == 0 || $this->lista_productos[$index]['cantidad'] < 0) {
            $this->lista_productos[$index]['cantidad'] = 1;
        }
    }
}