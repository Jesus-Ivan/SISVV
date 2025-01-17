<?php

namespace App\Livewire\Almacen\Existencias;

use App\Models\DetallesCompra;
use App\Models\OrdenCompra;
use App\Models\Proveedor;
use App\Models\Unidad;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Reporte extends Component
{
    //Los campos de entrada correspondientes al folio que se busca o el articulo especifico
    public $folio_input, $articulo_input;
    //Casilla de sumar seleccion
    public $autosuma = true;
    //Lista de articulos que se van a reportar
    public $lista_articulos = [];

    public function searchRequisicion()
    {
        //Si hay folio de busqueda
        if ($this->folio_input) {
            //Buscar los detalles de la orden de compra (requisicion)
            $detalles_requi = DetallesCompra::where('folio_orden', $this->folio_input)->get();
            //Agregar al array los articulos
            foreach ($detalles_requi as $key => $articulo) {
                $this->lista_articulos[] = [
                    'codigo' => $articulo->codigo_producto,
                    'nombre' => $articulo->nombre,
                ];
            }
        }
    }

    public function removeItem($indexItem)
    {
        unset($this->lista_articulos[$indexItem]);
    }

    public function generarReporte($folio = 1)
    {
        $requisicion = OrdenCompra::with('user')->find($folio);
        $detalle = DB::table('detalles_compras')
            ->where('folio_orden', $folio)
            ->get();
        //Generamos array's, para busquedas indexadas
        $unidades = $this->generateIndex(Unidad::all(), 'id', 'descripcion');
        $proveedores = $this->generateIndex(Proveedor::all(), 'id', 'nombre');

        $data = [
            'requisicion' => $requisicion->toArray(),
            'detalle' => $this->convertJsonColums($detalle->toArray()),
            'unidades' => $unidades,
            'proveedores' => $proveedores,
        ];


        $pdf = Pdf::loadView('reportes.requisicion', $data);
        $pdf->setOption(['defaultFont' => 'Courier']);
        $pdf->setPaper([0, 0, 612.283, 792], 'landscape'); // Tamaño aproximado del US LETTER (216 x 279.4) mm

        $pdfFile = $pdf->save('test.pdf');

        return response()->download($pdf->download('sdfsd.pdf'), 'test.pdf');


        //return $pdf->stream('requisicion' . $folio . '.pdf');
    }

    private function generateIndex($collection, $primary_key, $name)
    {
        $aux = [];
        foreach ($collection as $key => $value) {
            $aux[$value->$primary_key] = $value->$name;
        }
        return $aux;
    }

    /**
     * Recibe los detalles de la requisicion, y convierte las columnas de "almacen, bar, barra, caddie, cafeteria, cocina"
     * en JSON.
     * Unicamente convierte dichas columnas del array de entrada.
     */
    private function convertJsonColums(array $detalles_requisicion)
    {
        $aux = array_map(function ($item) {
            $item->almacen = json_decode($item->almacen);
            $item->bar = json_decode($item->bar);
            $item->barra = json_decode($item->barra);
            $item->caddie = json_decode($item->caddie);
            $item->cafeteria = json_decode($item->cafeteria);
            $item->cocina = json_decode($item->cocina);
            return $item;
        }, $detalles_requisicion);

        return $aux;
    }

    public function render()
    {
        return view('livewire.almacen.existencias.reporte');
    }
}
