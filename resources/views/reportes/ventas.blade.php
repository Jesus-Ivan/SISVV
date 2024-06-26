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

    td {
        border-bottom: 1px solid black;
    }
</style>



<h3>VISTA VERDE COUNTRY CLUB</h3>
<h5>Reporte de ventas</h5>
@if (@isset($caja))
    <table>
        <tbody>
            <tr>
                <td style="border: 0px">Corte de caja: {{ $caja->corte }}</td>
                <td style="border: 0px">Fecha apertura: {{ $caja->fecha_apertura }}</td>
            </tr>
            <tr>
                <td style="border: 0px">Vendedor: {{ $caja->users->name }}</td>
                <td style="border: 0px">Punto de venta: {{ $caja->puntoVenta->nombre }}</td>
            </tr>
            <tr>
                <td style="border: 0px">Cambio inicial: ${{ $caja->cambio_inicial }}</td>
                <td style="border: 0px"></td>
            </tr>
        </tbody>
    </table>
@endif
@foreach ($detalles_pagos as $key => $pagos)
    <p>{{ $key }}</p>
    <table>
        <thead>
            <tr>
                <th style="width: 50pt">F.Venta</th>
                <th style="width: 100%">Socio</th>
                <th>Subtotal</th>
                <th>Propina</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pagos as $pago)
                <tr>
                    <td>{{ $pago->folio_venta }}</td>
                    <td>{{ $pago->nombre }}</td>
                    <td>{{ $pago->monto }}</td>
                    <td>{{ $pago->propina }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td style="border: 0px"></td>
                <td style="text-align: right; border: 0px ">Total:</td>
                <td style="border: 0px">${{ array_sum(array_column($pagos->toArray(), 'monto')) }}</td>
                <td style="border: 0px"></td>
            </tr>
        </tfoot>
    </table>
@endforeach
<hr>
<p>Total de venta: ${{ $totalVenta }}</p>
