<style>
    html {
        margin: 10pt;
        font-size: x-small;
        font-weight: bold;
    }

    p {
        margin-top: 2pt;
        margin-bottom: 2pt;
    }

    .mayus {
        text-transform: uppercase;
    }

    .puntos {
        max-width: 40pt;
        text-align: left;
        overflow: hidden;
        /* Oculta el contenido que no cabe */
        white-space: nowrap;
        /* Evita que el texto se envuelva en varias líneas */
        text-overflow: ellipsis;
        /* Muestra "..." al final del texto */
    }

    h1,
    h2,
    h3,
    h4 {
        text-align: center;
        margin-top: 2pt;
        margin-bottom: 2pt;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        justify-content: initial;
        padding: 1pt;
    }

    th,
    td {
        line-height: 8pt;
        padding: 1pt;
        text-align: left;
    }
</style>
<?php
$path = 'storage/image001.jpg';
$type = pathinfo($path, PATHINFO_EXTENSION);
$data = file_get_contents($path);
$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
?>
<div>
    <div style="text-align: center;">
        <img src="<?php echo $base64; ?>" height="30" alt="logoVistaVerde" />
    </div>
    <p>RFC: {{ $rfc }}</p>
    <p>{{ $direccion }}</p>
    <p>Tel: 238{{ $telefono }}</p>
</div>
<hr>
<table>
    <tbody>
        <tr>
            <td style="width: 20mm">Folio: {{ $folio }}</td>
            <td>{{ $fecha }}</td>
        </tr>
        <tr>
            <td>Cliente: {{ $socio_id }}</td>
            <td>{{ $socio_nombre }}</td>
        </tr>
        <tr>
            <td>Vendedor:</td>
            <td class="mayus">{{ count($caja) > 0 ? $caja[0]->users->name : 'ERR' }}</td>
        </tr>
        <tr>
            <td>Punto:</td>
            <td>{{ $puntoVenta ? $puntoVenta->nombre : 'ERRPV' }}</td>
        </tr>
        <tr>
            <td>Tipo de venta:</td>
            <td class="mayus">{{ $tipo_venta ? $tipo_venta : 'ERRTV' }}</td>
        </tr>
    </tbody>
</table>
<hr>
<table>
    <thead>
        <tr>
            <th style="width: 35mm">Descripcion</th>
            <th>Cant.</th>
            <th>Precio</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($productos as $producto)
            <tr>
                <td>{{ $producto->catalogoProductos->nombre }}</td>
                <td style="text-align: center">{{ $producto->cantidad }}</td>
                <td>{{ $producto->precio }}</td>
                <td>{{ $producto->subtotal }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<hr>
<p style="text-align: right; margin-right: 10pt; font-size: 11pt">TOTAL:
    ${{ array_sum(array_column($productos->toArray(), 'subtotal')) }}</p>
@if (count($pagos))
    <h4>DETALLES DE PAGO</h4>
    <table>
        <thead>
            <tr>
                <th style="width: 40mm">Cliente</th>
                <th>Monto</th>
                <th>T.Pago</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pagos as $pago)
                @if ($pago->monto != 0)
                    <tr>
                        <td>{{ $pago->nombre }}</td>
                        <td>{{ $pago->monto }}</td>
                        <td class="puntos">{{ $pago->tipoPago->descripcion }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
    <hr>
    <p style="text-align: right; margin-right: 10pt; font-size: 11pt"> TOTAL: ${{ $total }}</p>
    @if ($pagos->where('propina', '>', 0)->count() > 0)
        <br>
        <h4>PROPINA</h4>
        <table>
            <thead>
                <tr>
                    <th style="width: 40mm">Cliente</th>
                    <th>Monto</th>
                    <th>T.Pago</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pagos as $pago)
                    @if ($pago->propina > 0)
                        <tr>
                            <td>{{ $pago->nombre }}</td>
                            <td class="puntos">{{ $pago->propina }}</td>
                            <td>{{ $pago->tipoPago->descripcion }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    @endif
    <br>
    <h3>GRACIAS POR SU COMPRA</h3>
@else
    <div style="width: 100%">
        <table style="width: 60%; justify-content: right">
            <tr>
                <td>PROPINA: </td>
                <td
                    style="border-bottom: 1pt; width: 100%; border-bottom-color: black; border-width: 1pt; border-bottom-style: double">
                </td>
            </tr>
        </table>
    </div>
    <br>
    <h4>IMPRESION NO VALIDA COMO COMPROBANTE DE PAGO</h4>
@endif
