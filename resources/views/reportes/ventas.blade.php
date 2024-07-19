<style>
    html {
        font-size: x-small
    }

    h3,
    h5 {
        line-height: 2pt;
        text-align: center;
    }

    p {
        margin: 2px
    }

    table {
        width: 100%;
    }

    thead {
        border-top: 1px solid black;
        border-bottom: 1px solid black;
    }
</style>

<h3>VISTA VERDE COUNTRY CLUB</h3>
<h3>Reporte de ventas</h3>
@if (@isset($header))
    <h3>{{ $header['fInicio'] }}</h3>
@endif
@if (@isset($caja))
    <table>
        <tbody>
            <tr>
                <td style="border: 0px">Corte de caja: {{ $caja->corte }}</td>
                <td style="border: 0px">Fecha apertura: {{ $caja->fecha_apertura }}</td>
            </tr>
            <tr>
                <td style="border: 0px">Vendedor: {{ $caja->users->name }}</td>
            </tr>
            <tr>
                <td style="border: 0px">Cambio inicial: ${{ $caja->cambio_inicial }}</td>
                <td style="border: 0px"></td>
            </tr>
        </tbody>
    </table>
@endif
@foreach ($detalles_pagos as $key => $pagos)
    @if (count($pagos))
        <h2>{{ $key }}</h2>
        <table>
            <thead>
                <tr>
                    <th>Folio</th>
                    <th style="width: 50pt">Fecha</th>
                    <th style="width: 60pt">Tipo Venta</th>
                    <th></th>
                    <th style="width: 100%">Socio</th>
                    <th>Total</th>
                    <th>Propina</th>
                    <th>Zona</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pagos as $pago)
                    <tr>
                        <td>{{ $pago->folio_venta }}</td>
                        <td>{{ substr($pago->fecha_apertura, 0, 10) }}</td>
                        <td style="text-transform: uppercase">{{ $pago->tipo_venta }}</td>
                        <td style="text-align: right">{{ $pago->id_socio }}</td>
                        <td style="text-transform: uppercase">{{ $pago->nombre }}</td>
                        <td style="text-align: right">{{ $pago->monto }}</td>
                        <td style="text-align: right">{{ $pago->propina }}</td>
                        <td style="text-align: right">{{ $puntos_venta[$pago->clave_punto_venta] }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <br>
                <tr>
                    <td style="border: 0px"></td>
                    <td style="border: 0px"></td>
                    <td style="border: 0px"></td>
                    <td style="border: 0px"></td>
                    <td style="text-align: right; font-size: 14px; font-weight: bold;">SUBTOTAL:</td>
                    <td style="text-align: right; font-size: 14px; font-weight: bold;">
                        ${{ array_sum(array_column($pagos->toArray(), 'monto')) }}</td>
                    <td style="text-align: right; font-size: 14px; font-weight: bold;">
                        ${{ array_sum(array_column($pagos->toArray(), 'propina')) }}</td>
                </tr>
            </tfoot>
        </table>
    @endif
@endforeach
<hr>
<h3>TOTAL DE VENTA: ${{ $totalVenta }}</h3>
