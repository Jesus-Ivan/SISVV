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
        min-width: 80pt;
        max-width: 120pt;
        text-align: left;
        overflow: hidden;
        /* Oculta el contenido que no cabe */
        white-space: nowrap;
        /* Evita que el texto se envuelva en varias líneas */
        text-overflow: ellipsis;
        /* Muestra "..." al final del texto */
    }

    .puntos-pendiente {
        min-width: 90pt;
        max-width: 100pt;
        text-align: left;
        overflow: hidden;
        /* Oculta el contenido que no cabe */
        white-space: nowrap;
        /* Evita que el texto se envuelva en varias líneas */
        text-overflow: ellipsis;
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
<hr>
<h3>PENDIENTES PAGADAS</h3>
@foreach ($detalles_pendientes as $key => $pagos)
    @if (count($pagos))
        <p class="remarcardo">{{ $key }}</p>
        <table>
            <thead>
                <tr>
                    <th style="width: 50pt">Venta</th>
                    <th style="text-align:left">Socio</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pagos as $pago)
                    <tr>
                        <td style="text-align: right">
                            <div>
                                {{ substr($pago->fecha_apertura, 0, 10) }}
                                {{ $pago->folio_venta }}
                            </div>
                        </td>
                        <td class="puntos-pendiente" style="vertical-align: bottom">
                            <p style=" margin-top: 0pt;  margin-bottom: 0pt;">
                                {{ $pago->id_socio }} {{ $pago->nombre }}
                            </p>
                        </td>
                        <td style="text-align: right; vertical-align: bottom">{{ $pago->monto }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td style="border: 0px"></td>
                    <td style="text-align: right; border: 0px ">Subtotal:</td>
                    <td style="border: 0px ">$ {{ array_sum(array_column($pagos->toArray(), 'monto')) }}</td>
                    <td style="border: 0px"></td>
                </tr>
            </tfoot>
        </table>
    @endif
@endforeach
<br>
<p style="font-size: 11pt">Total de venta: ${{ $totalVenta }}</p>
<div>
    @if (@isset($caja))
        @if (!$caja->fecha_cierre)
            <p style="text-align: center">IMPRESION NO VALIDA COMO CORTE FINAL</p>
        @endif
    @endif
</div>
