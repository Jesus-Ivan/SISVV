<style>
    html {
        margin: 10pt;
        font-size: x-small;
        font-weight: bold
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
        font-style: normal;
        font-size: 12pt;
    }

    .puntos {
        width: 50pt;
        max-width: 120pt;
        text-align: left;
        overflow: hidden;
        /* Oculta el contenido que no cabe */
        white-space: nowrap;
        /* Evita que el texto se envuelva en varias líneas */
        text-overflow: ellipsis;
        /* Muestra "..." al final del texto */
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
    <div>
        <p>Fecha apertura: {{ $caja->fecha_apertura }}</p>
        <p>Fecha cierre: {{ $caja->fecha_cierre }}</p>
        <p>Corte de caja: {{ $caja->corte }}</p>
    </div>
    <hr>
    <p style="border: 0px">Vendedor: {{ $caja->users->name }}</p>
    <p style="border: 0px">Punto de venta: {{ $caja->puntoVenta->nombre }}</p>
    <p style="border: 0px">Cambio inicial: ${{ $caja->cambio_inicial }}</p>
@endif
<hr>
@foreach ($detalles_pagos as $key => $pagos)
    @if (count($pagos))
        <p class="remarcardo">{{ $key }}</p>
        <table>
            <thead>
                <tr>
                    <th>Venta</th>
                    <th style="text-align:left">Socio</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pagos as $pago)
                    <tr>
                        <td>{{ $pago->folio_venta }}</td>
                        <td class="puntos">{{ $pago->id_socio }} {{ $pago->nombre }}</td>
                        <td style="text-align: right">{{ $pago->monto }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td style="border: 0px"></td>
                    <td style="text-align: right; border: 0px ">Subtotal:</td>
                    <td style="border: 0px">$ {{ array_sum(array_column($pagos->toArray(), 'monto')) }}</td>
                    <td style="border: 0px"></td>
                </tr>
            </tfoot>
        </table>
    @endif
@endforeach
<hr>
<p style="font-size: 11pt">Total de venta: ${{ $totalVenta }}</p>
