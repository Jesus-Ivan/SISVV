<?php

namespace App\Livewire\Almacen\Mermas;

use App\Constants\AlmacenConstants;
use Illuminate\Support\Facades\DB;
use App\Models\Bodega;
use App\Models\CatalogoVistaVerde;
use App\Models\MermaGeneral;
use App\Models\Stock;
use App\Models\TipoMerma;
use App\Models\Unidad;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Principal extends Component
{
    use WithPagination;
    //Propiedad que almacena el articulo seleccionado del modal
    #[Locked]
    public $articulo = ['codigo' => null, 'nombre' => null];
    public $stock_unitario, $stock_peso;
    //Propiedades complementarias del modal
    public $tipo_merma, $origen_merma, $cantidad, $id_unidad, $descontar = true, $observaciones;
    //Propiedad que almacena al usuario autenticado
    #[Locked]
    public $usuario;
    public $merma_seleccionada, $mes_seleccionado;

    //Hook de inicio del componente
    public function mount()
    {
        $this->usuario = auth()->user();
        //Establecemos los parametros iniciales
        $this->merma_seleccionada = null;
        $this->mes_seleccionado = now()->toDateString();
        //Limpiar pagina
        $this->resetPage();
    }

    #[On('selected-articulo')]
    public function onSelectedArticulo(CatalogoVistaVerde $articulo)
    {
        //Guardamos los valores del articulo
        $this->articulo = $articulo->toArray();
        //Buscar los stocks
        $this->buscarStocks($articulo->codigo);
    }

    #[Computed()]
    public function bodegas()
    {
        return Bodega::where('tipo', AlmacenConstants::BODEGA_INTER_KEY)->get();
    }

    #[Computed()]
    public function unidades()
    {
        return Unidad::where('descripcion', 'like', 'KG')
            ->orWhere('descripcion', 'like', 'PIEZA')
            ->orWhere('descripcion', 'like', 'LITRO')
            ->limit(3)
            ->get();
    }

    #[Computed()]
    public function tipoMerma()
    {
        return TipoMerma::all()->groupBy('tipo');
    }

    #[Computed()]
    public function mermas()
    {
        if ($this->merma_seleccionada) {
            return MermaGeneral::with(['bodega', 'unidad', 'tipo'])
                ->where('clave_bodega_origen', $this->merma_seleccionada)
                ->whereMonth('created_at', Carbon::parse($this->mes_seleccionado)->month)
                ->orderBy('folio', 'DESC')
                ->paginate(10);
        } else {
            return MermaGeneral::with(['bodega', 'unidad', 'tipo'])
                ->whereMonth('created_at', Carbon::parse($this->mes_seleccionado)->month)
                ->orderBy('folio', 'DESC')
                ->paginate(10);
        }
    }

    public function changedOrigen($eValue)
    {
        //Buscar los stocks
        $this->buscarStocks($this->articulo['codigo']);
    }

    /**
     * Registra la merma en la tabla 'mermas_generales'
     */
    public function registrarMerma()
    {
        $validated = $this->validate([
            'articulo' => 'required',
            'tipo_merma' => 'required',
            'origen_merma' => 'required',
            'cantidad' => 'required|numeric',
            'id_unidad' => 'required',
        ]);
        try {
            DB::transaction(function () use ($validated) {
                //Registrar la merma
                MermaGeneral::create([
                    'codigo_catalogo' => $validated['articulo']['codigo'],
                    'nombre' =>  $validated['articulo']['nombre'],
                    'clave_bodega_origen' => $validated['origen_merma'],
                    'cantidad' => $validated['cantidad'],
                    'id_unidad' => $validated['id_unidad'],
                    'id_tipo_merma' => $validated['tipo_merma'],
                    'usuario' => $this->usuario->name,
                    'observaciones' => $this->observaciones
                ]);
                //Restar el stock
                $this->restarStock(
                    $validated['articulo']['codigo'],
                    $validated['cantidad'],
                    $validated['id_unidad'],
                    $validated['origen_merma']
                );
            });

            //flash message
            session()->flash('success', 'Se registro la merma correctamente');
            $this->reset();
        } catch (\Throwable $th) {
            //flash message
            session()->flash('fail', $th->getMessage());
        }
        //Evento
        $this->dispatch('open-action-message');
        //cerrar el modal
        $this->dispatch('close-modal');
    }

    /**
     * Busca los stocks de un articulo y los guarda en las propiedades del componente
     */
    private function buscarStocks($codigo)
    {
        $this->stock_unitario = Stock::where([
            ['tipo', '=', AlmacenConstants::CANTIDAD_KEY],
            ['codigo_catalogo', '=', $codigo]
        ])->first();
        $this->stock_peso = Stock::where([
            ['tipo', '=', AlmacenConstants::PESO_KEY],
            ['codigo_catalogo', '=', $codigo]
        ])->first();
    }

    /**
     * Resta el stock del articulo dado, en la bodega correspondiente
     */
    private function restarStock($codigo, $cantidad, $id_unidad, $origen_merma) {}



    public function render()
    {
        return view('livewire.almacen.mermas.principal');
    }
}
