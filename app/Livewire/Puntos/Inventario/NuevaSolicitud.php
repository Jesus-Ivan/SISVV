<?php

namespace App\Livewire\Puntos\Inventario;

use App\Libraries\InventarioService;
use App\Models\DetallesSolicitudPedido;
use App\Models\Insumo;
use App\Models\SolicitudPedido;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Component;

class NuevaSolicitud extends Component
{
    public $hoy;
    public $search_input = '';
    public $selectedItems = [];
    public $lista_productos = [];

    public $codigopv;
    public $permisospv;

    public function mount($codigopv, $permisospv)
    {
        $this->hoy = now();
        $this->codigopv = $codigopv;
        $this->permisospv = $permisospv;
    }

    //Elimina un articulo de la lista
    public function eliminarArticulo($index)
    {
        unset($this->lista_productos[$index]);
    }

    //Mostamos la lista de insumos en el modal
    #[Computed()]
    public function insumos()
    {
        $result = Insumo::whereAny(['descripcion', 'clave'], 'like', "%$this->search_input%")
            ->where('inventariable', true);

        return $result->get()->take(50);
    }

    public function finalizarSeleccion()
    {
        //Crear instacia del servicio del inventario
        $inventario = new InventarioService();
        //Obtener fecha y hora actuales
        $hoy = now();
        //Obtenemos la clave de la bodega mediante la clave de pv
        $claveBodega = "stock_" . strtolower($this->codigopv);

        //Filtramos los productos seleccionados
        $total_seleccionados = array_filter($this->selectedItems);

        //Recorre todo el array de seleccionados
        foreach ($total_seleccionados as $clave => $value) {
            //Se busca el insumo del producto en base a su clave.
            $producto = Insumo::with('unidad')->find($clave);

            if ($producto) {
                $existencias = $inventario->existenciasInsumo(
                    $producto->clave,
                    $hoy->toDateString(),
                    $hoy->toTimeString(),
                    $claveBodega
                );

                // Se anexa el producto al array de la tabla
                $this->lista_productos[] = [
                    'clave' => $producto->clave,
                    'descripcion' => $producto->descripcion,
                    'existencia' => $existencias[0]['existencias_insumo'] ?? 0,
                    'cantidad' => 1,
                    'unidad' => $producto->unidad,
                ];
            }
        }

        //Limpiar articulos seleccionados y barra de busqueda
        $this->reset(['selectedItems', 'search_input']);
        //Cerramos el modal
        $this->dispatch('close-modal', name: 'modal-productos');
    }

    public function aplicarSolicitud()
    {
        //Obtener el usuario autenticado actualmente
        $user = auth()->user();

        //Validamos datos adicionales
        $validated = $this->validate([
            'lista_productos' => 'min:1|required'
        ]);

        //Iniciamos la transaccion
        try {
            DB::transaction(function () use ($user, $validated) {
                $claveBodega = "stock_" . strtolower($this->codigopv);
                //Registro de pedido
                $result = SolicitudPedido::create([
                    'id_user' => $user->id,
                    'user_name' => $user->name,
                    'clave_origen' => $claveBodega,
                    'fecha_existencias' => $this->hoy
                ]);

                foreach ($validated['lista_productos'] as $key => $row) {
                    DetallesSolicitudPedido::create([
                        'folio_pedido' => $result->folio,
                        'clave_insumo' => $row['clave'],
                        'descripcion' => $row['descripcion'],
                        'existencias' => $row['existencia'],
                        'cantidad_insumo' => $row['cantidad']
                    ]);
                }
            });

            $this->reset();
            session()->flash('success', 'Solicitud registrada exitosamente.');
            $this->dispatch('open-action-message');
        } catch (ValidationException $e) {
            //Si es una excepcion de validacion, volverla a lanzar a la vista
            throw $e;
        } catch (\Throwable $th) {
            //Enviamos flash message, al action-message (En cualquier otro caso de excepcion)
            session()->flash('fail', $th->getMessage());
        }
    }

    /**
     * Evitamos que el usuario ingrese valores negativos o vacios en la cantidad
     */
    public function actualizarCantidad($index)
    {
        //Verificamos que cantidad no este vacia o negativa
        if (strlen($this->lista_productos[$index]['cantidad']) == 0 || $this->lista_productos[$index]['cantidad'] < 0)
            $this->lista_productos[$index]['cantidad'] = 1;
    }

    public function render()
    {
        return view('livewire.puntos.inventario.nueva-solicitud');
    }
}
