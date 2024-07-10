<style>
    html {
        margin: 10pt;
        font-size: 7pt;
    }

    p {
        margin-top: 2pt;
        margin-bottom: 2pt;
    }

    .mayus {
        text-transform: uppercase;
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
        padding: 0pt;
    }

    th,
    td {
        line-height: 8pt;
        padding: 0pt;
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
<p style="text-align: right;">TOTAL: ${{ array_sum(array_column($productos->toArray(), 'subtotal')) }}</p>
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
                <tr>
                    <td>{{ $pago->nombre }}</td>
                    <td>{{ $pago->monto }}</td>
                    <td>{{ $pago->tipoPago->descripcion }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <hr>
    <p style="text-align: right;"> TOTAL: ${{ $total }}</p>
    <h3>GRACIAS POR SU COMPRA</h3>
@else
    <br>
    <h4>IMPRESION NO VALIDA COMO COMPROBANTE DE PAGO</h4>
@endif
