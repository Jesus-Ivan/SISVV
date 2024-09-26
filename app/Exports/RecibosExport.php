<?php

namespace App\Exports;

use App\Models\Cuota;
use App\Models\SocioCuota;
use App\Models\SocioMembresia;
use App\Models\TipoPago;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;

class RecibosExport implements FromArray
{
    protected $recibos;
    protected $cuotas;
    protected $metodos_pago;

    public function __construct(array $recibos)
    {
        $this->recibos = $recibos;
        $this->cuotas = Cuota::all();
        $this->metodos_pago = TipoPago::all();
    }
    public function array(): array
    {
        //Definimos los titulos de los encabezados a la tabla
        $encabezados = [
            'FOLIO' => 'FOLIO',
            'FECHA' => 'FECHA',
            'HORA' => 'HORA',
            'NO.SOCIO' => 'NO.SOCIO',
            'NOMBRE' => 'NOMBRE',
            'FORMA PAGO' => 'FORMA PAGO',
            'TOTAL' => 'TOTAL',
            'CUOTA MEMBRESIA MENSUAL' => 'CUOTA MEMBRESIA MENSUAL',
            'MEMBRESIA ANUAL' => 'MEMBRESIA ANUAL',
            'LOCKER MENSUAL' => 'LOCKER MENSUAL',
            'LOCKER ANUAL' => 'LOCKER ANUAL',
            'CARRITO MENSUAL' => 'CARRITO MENSUAL',
            'CARRITO ANUAL' => 'CARRITO ANUAL',
            'CONSUMO MINIMO MENSUAL' => 'CONSUMO MINIMO MENSUAL',
            'RECARGOS 3%' => 'RECARGOS 3%',
            'BAR' => 'BAR',
            'CADDIE' => 'CADDIE',
            'CAFETERIA' => 'CAFETERIA',
            'LOCKERS' => 'LOCKERS',
            'RECEPCION' => 'RECEPCION',
            'RESTAURANT' => 'RESTAURANT',
            'BUGABAR' => 'BUGABAR',
            'EVENTOS' => 'EVENTOS',
            'ESTETICA' => 'ESTETICA',
            'PROPINAS' => 'PROPINAS',
            'SALDO A FAVOR' => 'SALDO A FAVOR',
            'FEDERACION' => 'FEDERACION',
            'CURSOS' => 'CURSOS',
            'TORNEOS' => 'TORNEOS',
            'TIPO CUOTA' => 'TIPO CUOTA',
            'OBSERVACIONES' => 'OBSERVACIONES',
        ];

        //Filtramos de todas las cuotas, aquellas que son cuotas para alguna membresia (mensual o inactiva o extraordinaria)
        $cuotas_mensualidades = array_filter($this->cuotas->toArray(), function ($cuota) {
            return $cuota['tipo'] == 'INA' || $cuota['tipo'] == 'MEN' || $cuota['tipo'] == 'EXT';
        });
        //Filtramos de todas las cuotas, aquellas que son cuotas para alguna membresia (anual)
        $cuotas_membresia_anual = array_filter($this->cuotas->toArray(), function ($cuota) {
            return $cuota['tipo'] == 'ANU';
        });
        //Filtramos de todas las cuotas, aquellas que son cuotas de un curso
        $cuotas_cursos = array_filter($this->cuotas->toArray(), function ($cuota) {
            return $cuota['tipo'] == 'CUR';
        });
        //Filtramos las cuotas, que pertenecen a torneos
        $cuotas_torneos = array_filter($this->cuotas->toArray(), function ($cuota) {
            return $cuota['tipo'] == 'TOR';
        });

        //array auxiliar
        $data = [];

        //Agregamos los encabezados al array
        $data[] = $encabezados;

        //para cada recibo
        foreach ($this->recibos as $key => $recibo) {
            //corregimos la fecha del array, segun la hora correcta.
            $fecha_aux = $this->convertHours($recibo['created_at']);

            //Buscamos los detalles del recibo
            $detalles_recibo = $this->detallesRecibo($recibo['folio']);

            foreach ($this->metodos_pago as $metodo) {
                $detalles_filtrados = array_filter($detalles_recibo->toArray(), function ($detalle) use ($metodo) {
                    return $detalle->id_tipo_pago == $metodo->id;
                });
                //Si no hubo detalles del  recibo, con el metodo de pago en la iteracion actual, omitir
                if (!count($detalles_filtrados))
                    continue;
                //Ingresamos los valores al array
                $data[] = [
                    'FOLIO' => $recibo['folio'],
                    'FECHA' => substr($fecha_aux, 0, 10),
                    'HORA' => substr($fecha_aux, 11, 8),
                    'NO.SOCIO' => $recibo['id_socio'],
                    'NOMBRE' => $recibo['nombre'],
                    'FORMA PAGO' => $metodo->descripcion,
                    'TOTAL' => $recibo['total'],
                    'CUOTA MEMBRESIA MENSUAL' => $this->totalCuotas($cuotas_mensualidades, $detalles_filtrados),
                    'MEMBRESIA ANUAL' => $this->totalCuotas($cuotas_membresia_anual, $detalles_filtrados),
                    'LOCKER MENSUAL' => $this->totalCuota(1, $detalles_filtrados),    // el id de cuota del locker es: 1
                    'LOCKER ANUAL' => $this->totalCuota(40, $detalles_filtrados),    // el id de cuota del locker es: 40
                    'CARRITO MENSUAL' => $this->totalCuota(15, $detalles_filtrados),  // el id del resguardo de carrito es: 15
                    'CARRITO ANUAL' => $this->totalCuota(41, $detalles_filtrados),  // el id del resguardo de carrito es: 41
                    'CONSUMO MINIMO MENSUAL' => $this->totalCuota(2, $detalles_filtrados), // 2 : diferencia de consumo 
                    'RECARGOS 3%' => $this->totalCuota(16, $detalles_filtrados),      // 16: recargo 3%
                    'BAR' => $this->totalConcepto($detalles_filtrados, "(BAR LOUNGE|\d+ - BAR)"),
                    'CADDIE' => $this->totalConcepto($detalles_filtrados, "CADDIE"),
                    'CAFETERIA' => $this->totalConcepto($detalles_filtrados, "(CAFETERIA|CAFETERÍA)"),
                    'LOCKERS' => $this->totalConcepto($detalles_filtrados, "LOCKER"),
                    'RECEPCION' => $this->totalConcepto($detalles_filtrados, "RECEPCION"),
                    'RESTAURANT' => $this->totalConcepto($detalles_filtrados, "RESTAURANT"),
                    'BUGABAR' => $this->totalConcepto($detalles_filtrados, "BUGABAR"),
                    'EVENTOS' => $this->totalCuota(20, $detalles_filtrados),
                    'ESTETICA' => $this->totalCuota(19, $detalles_filtrados),
                    'PROPINAS' => $this->totalConcepto($detalles_filtrados, "propina"),
                    'SALDO A FAVOR' => $this->totalSaldoFavor($detalles_filtrados),
                    'FEDERACION' => $this->totalCuota(17, $detalles_filtrados),     //17: corresponde a la federacion
                    'CURSOS' => $this->totalCuotas($cuotas_cursos, $detalles_filtrados),
                    'TORNEOS' => $this->totalCuotas($cuotas_torneos, $detalles_filtrados),
                    'TIPO CUOTA' => $this->tipoCuota($recibo['id_socio']),
                    'OBSERVACIONES' => $recibo['observaciones'],
                ];
            }
        }

        return $data;
    }

    /** 
     * Se encarga de convertir la hora UTC, que vienene en String.
     * A una fecha con la hora de la zona America_Mexico en String
     */
    private function convertHours(string $date)
    {
        $fecha_aux = Carbon::parse($date)->subHours(6);
        return $fecha_aux->toISOString();
    }

    /** 
     * Obtiene los detalles del recibo, unido al estado de cuenta
     * a traves de un join
     */
    private function detallesRecibo($folio)
    {
        return DB::table('detalles_recibo')
            ->join('estados_cuenta', 'detalles_recibo.id_estado_cuenta', '=', 'estados_cuenta.id')
            ->where('detalles_recibo.folio_recibo', '=', $folio)
            ->select(
                'estados_cuenta.id',
                'estados_cuenta.id_venta_pago',
                'estados_cuenta.folio_evento',
                'estados_cuenta.id_cuota',
                'estados_cuenta.id_socio',
                'estados_cuenta.concepto',
                'estados_cuenta.consumo',
                'detalles_recibo.*'
            )
            ->get();
    }

    /** 
     * Obtenemos el tipo de cuota para el reporte, apartir de la tabla 'socios_membresia'
     */
    private function tipoCuota($id_socio)
    {
        //Buscamos la clave de membresia correspondiente al socio
        $socio_membresia = SocioMembresia::where('id_socio', $id_socio)
            ->first();

        if ($socio_membresia)
            return substr($socio_membresia->clave_membresia, 0, 2);
    }

    /**
     * Calcula la suma de los conceptos del recibo si algun concepto coincide con algun id del grupo de cuotas
     */
    private function totalCuotas($cuotas, $detalles_recibo)
    {
        //Variable para acumular el total
        $total = 0;

        foreach ($detalles_recibo as $detalle) {
            //Del concepto del recibo, buscamos si esta en la lista de cuotas
            $cuotas_result = array_filter($cuotas, function ($cuota) use ($detalle) {
                return $detalle->id_cuota == $cuota['id'];
            });
            //Si coincidio con una cuota
            if (count($cuotas_result)) {
                $total += $detalle->monto_pago;        //Acumular el monto del pago
            }
        }
        return $total;
    }

    /**
     * Obtiene la suma de una cuota individual, dado los detalles del recibo
     */
    private function totalCuota($id_cuota, $detalles_recibo)
    {
        //Filtramos todas las cuotas (que coincidan con el id) que se pagaron en el recibo
        $cuotas_recibo = array_filter($detalles_recibo, function ($detalle) use ($id_cuota) {
            return $detalle->id_cuota == $id_cuota;
        });
        return array_sum(array_column($cuotas_recibo, 'monto_pago'));
    }

    /**
     * Obtiene el monto total de los detalles del recibo, correspondiente a una venta y un patron 
     * de expresion regular presente al concepto
     */
    private function totalConcepto($detalles_recibo, $exp_reg)
    {
        //Creamos patron de expresion regular
        $patron = "/$exp_reg/i";
        //Filtramos todos los detalles que coincidan con el patron
        $notas_venta = array_filter($detalles_recibo, function ($detalle) use ($patron) {
            return is_null($detalle->id_cuota) && preg_match($patron, $detalle->concepto);
        });
        return array_sum(array_column($notas_venta, 'monto_pago'));
    }

    private function totalSaldoFavor($detalles_recibo)
    {
        $total = array_sum(array_column($detalles_recibo, 'saldo_favor_generado'));
        if ($total)
            return  $total;
    }
}
