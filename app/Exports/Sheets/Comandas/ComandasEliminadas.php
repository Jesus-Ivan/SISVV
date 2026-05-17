<?php

namespace App\Exports\Sheets\Comandas;

use App\Models\ConceptoCancelacion;
use App\Models\DetallesVentaProducto;
use App\Models\EstadoProductoVenta;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;

use Maatwebsite\Excel\Concerns\WithTitle;

class ComandasEliminadas implements FromArray, WithTitle
{
    protected $ventas;
    protected $motivos_eliminaciones = [];
    protected $estado = [];

    public function __construct(array $ventas)
    {
        $this->ventas = $ventas;

        $result = ConceptoCancelacion::all();
        foreach ($result as $key => $motivo) {
            $this->motivos_eliminaciones[$motivo->id] = $motivo->descripcion;
        }
        $result_estado = EstadoProductoVenta::all();
        foreach ($result_estado as $key => $value) {
            $this->estado[$value['id']] = $value['descripcion'];
        }
    }

    public function array(): array
    {
        //Array auxiliar de los productos.
        $productos = [];
        //Agregamos el encabezado
        $productos[] = [
            'id_socio' => 'NO.SOCIO',
            'nombre' => 'NOMBRE',
            'folio_venta' => '#VENTA',
            'punto' =>  'PUNTO VENTA',
            'f_inicio' => 'FECHA INICIO',
            'f_fin' => 'FECHA FIN',
            'estado' => 'ESTADO',
            'clave_producto' => 'CLAVE PROD.',
            'descripcion' => 'DESCRIPCION',
            'cantidad' => 'CANTIDAD',
            'observaciones' => 'OBSERVACIONES',
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
                    'id_socio' => $venta['id_socio'],
                    'nombre' => $venta['nombre'],
                    'folio_venta' => $producto['folio_venta'],
                    'punto' =>  $venta['punto_venta']['nombre'],
                    'f_inicio' => $producto['inicio'],
                    'f_fin' => $producto['terminado'],
                    'estado' => $this->estado[$producto['id_estado']],
                    'clave_producto' => $producto['clave_producto'],
                    'descripcion' => $producto['nombre'],
                    'cantidad' => $producto['cantidad'],
                    'observaciones' => $producto['observaciones'] . ' ' . $venta['observaciones'],
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
