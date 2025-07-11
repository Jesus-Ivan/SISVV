<style>
    html {
        font-size: small;
        margin: 16pt;
    }

    table {
        border: 0px;
        border-style: solid;
        border-color: black;
        width: 100%
    }

    tr,
    td {
        border: 1pt;
        border-top-style: solid;
        border-top-color: black;
    }

    .padding-cel {
        padding: 2pt;
    }

    .color-cel {
        background-color: #b4b4b4;
    }
</style>
<?php
$path = 'storage/image001.jpg';
$type = pathinfo($path, PATHINFO_EXTENSION);
$data = file_get_contents($path);
$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
?>

<head>
    <title>R {{ $requisicion['folio'] }}</title>
</head>
<table style="border: none">
    <tbody>
        <tr>
            <td style="border: none">
                <h2>REQUISICION DE COMPRA</h2>
            </td>
            <td style="border: none; text-align: right"><img src="<?php echo $base64; ?>" height="40"
                    alt="logoVistaVerde" /></td>
        </tr>
    </tbody>
</table>
<table>
    <thead>
        <tr class="color-cel">
            <th style="width: 10%">FOLIO</th>
            <th style="width: 10%">TIPO</th>
            <th style="width: 40%">SOLICITANTE</th>
            <th style="width: 20%">FECHA</th>
            <th style="width: 20%">CONSULTADO</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="text-align: center">{{ $requisicion['folio'] }}</td>
            <td style="text-align: center">{{ $requisicion['tipo_orden'] }}</td>
            <td>{{ $requisicion['user']['name'] }}</td>
            <td>{{ $requisicion['created_at'] }}</td>
            <td>{{ now() }}</td>
        </tr>
    </tbody>
</table>
<br>
<table>
    <thead>
        <tr class="color-cel">
            <th style="width: 6%">#</th>
            <th style="width: 35%">PRESENTACION</th>
            <th>CANT.</th>
            <th>P.UNITARIO</th>
            <th>P.IMPUESTO</th>
            <th>IMPORTE</th>
            <th>PROVEEDOR</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($detalle as $item)
            <tr>
                <td>{{ $item['clave_presentacion'] }}</td>
                <td>{{ $item['descripcion'] }}</td>
                <td>{{ $item['cantidad'] }}</td>
                <td>${{ $item['costo_unitario'] }}</td>
                <td>${{ $item['costo_con_impuesto'] }}</td>
                <td>${{ number_format($item['importe'], 2) }}</td>
                <td>{{ $proveedores[$item['id_proveedor']] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<br>
<div style="float: right; margin-right: 2%">
    <table style="width: 400pt">
        <tbody>
            <tr>
                <td class="padding-cel"
                    style="width: 80pt; background-color: #b4b4b4; border: 0pt; border-style: none;">SUBTOTAL</td>
                <td class="padding-cel" style="width: 94pt ; border: 0pt; border-style: none; ">
                    $ {{ number_format($subtotal, 2) }}</td>
                <td class="padding-cel" style=" background-color: #b4b4b4; border: 1pt; border-left-style: outset">
                    APROBADO
                    POR: (NOMBRE Y FIRMA)</td>
            </tr>
            <tr>
                <td class="padding-cel" style="background-color: #b4b4b4;">IVA</td>
                <td class="padding-cel">$ {{ number_format($iva, 2) }}</td>
                <td class="padding-cel" style="border: 1pt; border-left-style: outset"></td>
            </tr>
            <tr>
                <td class="padding-cel" style="background-color: #b4b4b4;">TOTAL</td>
                <td class="padding-cel">
                    ${{ number_format($subtotal + $iva, 2) }}
                </td>
                <td class="padding-cel" style="border: 1pt; border-left-style: outset"></td>
            </tr>
        </tbody>
    </table>
</div>
<table style="width: 350pt">
    <thead>
        <tr>
            <td class="padding-cel" style=" background-color: #b4b4b4; border: 1pt; border-left-style: none">OBSERVACIONES</td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="padding-cel" style="border: 1pt; border-left-style: none">{{ $requisicion->observaciones }}</td>
        </tr>
    </tbody>
</table>
