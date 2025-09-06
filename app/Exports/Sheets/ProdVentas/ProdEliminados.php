<?php

namespace App\Exports\Sheets\ProdVentas;

use App\Models\ConceptoCancelacion;
use App\Models\DetallesVentaProducto;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;

use Maatwebsite\Excel\Concerns\WithTitle;

class ProdEliminados implements FromArray, WithTitle
{
    protected $ventas;
    protected $motivos_eliminaciones = [];

    public function __construct(array $ventas)
    {
        $this->ventas = $ventas;

        $result = ConceptoCancelacion::all();
        foreach ($result as $key => $motivo) {
            $this->motivos_eliminaciones[$motivo->id] = $motivo->descripcion;
        }
    }

    public function array(): array
    {
        //Array auxiliar de los productos.
        $productos = [];
        //Agregamos el encabezado
        $productos[] = [
            'clave_catalogo' => '#CATALOGO',
            'clave_producto' => 'CLAVE PRODUCTO',
            'folio_venta' => 'FOLIO VENTA',
            'punto' =>  'PUNTO',
            'descripcion' => 'DESCRIPCION',
            'cantidad' => 'CANTIDAD',
            'precio' => 'PRECIO',
            'importe' => 'IMPORTE',
            'solicitante' => 'SOLICITANTE',
            'fecha' => 'FECHA ELIMINACION',
            'motivo' => 'MOTIVO ELIMINACION',
            'detalles_eliminacion' => 'DETALLES'
        ];
        foreach ($this->ventas as $venta) {
            //Para cada producto de una venta
            foreach ($venta['detalles_productos'] as $key => $producto) {
                //Lo agregamos al array
                $productos[] = [
                    'clave_catalogo' => $producto['codigo_catalogo'],
                    'clave_producto' => $producto['clave_producto'],
                    'folio_venta' => $producto['folio_venta'],
                    'punto' =>  $venta['punto_venta']['nombre'],
                    'descripcion' => $producto['nombre'],
                    'cantidad' => $producto['cantidad'],
                    'precio' => $producto['precio'],
                    'importe' => $producto['subtotal'],
                    'solicitante' => $producto['usuario_cancela'],
                    'fecha' => $this->convertHours($producto['deleted_at']),
                    'motivo' => $this->motivos_eliminaciones[$producto['id_cancelacion']],
                    'detalles_eliminacion' => $producto['motivo_cancelacion']
                ];
            }
        }
        //Retornamos el array listo
        return $productos;
    }

    /** 
     * Se encarga de convertir la hora UTC, que vienene en String.
     * A una fecha con la hora de la zona America_Mexico en String
     */
    private function convertHours(string $date)
    {
        $fecha_aux = Carbon::parse($date)->subHours(6)->toISOString();
        $fecha = substr($fecha_aux, 0, 10);
        $hora = substr($fecha_aux, 11, 8);
        return $fecha . " " . $hora;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Eliminados';
    }
}
