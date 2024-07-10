<style>
    html {
        margin: 10pt;
        font-size: 6pt;
    }

    h3,
    h5 {
        line-height: 2pt;
        text-align: center;
    }

    p {
        margin-top: 1pt;
        margin-bottom: 1pt;
    }

    .remarcardo {
        font-style: normal,
            font-weight: bold,
    }

    table {
        width: 100%;
        padding: 0pt;
    }

    thead {
        border-top: 1px solid black;
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
    <p class="remarcardo">{{ $key }}</p>
    <table>
        <thead>
            <tr>
                <th style="width: 30pt">Venta</th>
                <th style="width: 100%; text-align:left">Socio</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pagos as $pago)
                <tr>
                    <td>{{ $pago->folio_venta }}</td>
                    <td>{{ $pago->id_socio }} {{ $pago->nombre }}</td>
                    <td>{{ $pago->monto }}</td>
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
