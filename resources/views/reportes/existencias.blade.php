<style>
    html {
        font-size: small;
        margin: 16pt;
    }

    table {
        border: 1pt;
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

    p{
        text-transform: uppercase;
        margin: 0px
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
<table style="border: none">
    <tbody>
        <tr>
            <td style="border: none">
                <h2>REPORTE DE EXISTENCIAS {{ now() }}</h2>
            </td>
            <td style="border: none; text-align: right"><img src="<?php echo $base64; ?>" height="40"
                    alt="logoVistaVerde" /></td>
        </tr>
    </tbody>
</table>
<table>
    <thead>
        <tr class="color-cel">
            <th style="width: fit-content">CODIGO</th>
            <th style="width: 30%">NOMBRE</th>
            <th>ALMACEN</th>
            <th>BAR</th>
            <th>BARRA</th>
            <th>CADDIE</th>
            <th>CAFETERIA</th>
            <th>COCINA</th>
            <th>ULTIMA COMPRA</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($articulos as $articulo)
            <tr>
                <td>{{ $articulo['codigo'] }}</td>
                <td>{{ $articulo['nombre'] }}</td>
                <td>
                    @foreach ($articulo['stocks'] as $stock)
                        <p>
                            {{ substr($stock['tipo'], 0, 1) }} : {{ $stock['stock_alm'] }}
                        </p>
                    @endforeach
                </td>
                <td>
                    @foreach ($articulo['stocks'] as $stock)
                        <p>
                            {{ substr($stock['tipo'], 0, 1) }} : {{ $stock['stock_bar'] }}
                        </p>
                    @endforeach
                </td>
                <td>
                    @foreach ($articulo['stocks'] as $stock)
                        <p>
                            {{ substr($stock['tipo'], 0, 1) }} : {{ $stock['stock_res'] }}
                        </p>
                    @endforeach
                </td>
                <td>
                    @foreach ($articulo['stocks'] as $stock)
                        <p>
                            {{ substr($stock['tipo'], 0, 1) }}: {{ $stock['stock_cad'] }}
                        </p>
                    @endforeach
                </td>
                <td>
                    @foreach ($articulo['stocks'] as $stock)
                        <p>
                            {{ substr($stock['tipo'], 0, 1) }} : {{ $stock['stock_caf'] }}
                        </p>
                    @endforeach
                </td>
                <td>
                    @foreach ($articulo['stocks'] as $stock)
                        <p>
                            {{ substr($stock['tipo'], 0, 1) }} : {{ $stock['stock_coc'] }}
                        </p>
                    @endforeach
                </td>
                <td>{{ $articulo['ultima_compra'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
