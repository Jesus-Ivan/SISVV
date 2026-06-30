<style>
    html {
        font-size: x-small;
        margin: 16pt;
    }

    table {
        width: 100%
    }

    tr,
    td {
        border: 1pt;
        border-top-style: solid;
        border-top-color: black;
    }

    p {
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
                <h2>REPORTE DE EXISTENCIAS</h2>
            </td>
            <td style="border: none; text-align: right"><img src="<?php echo $base64; ?>" height="40"
                    alt="logoVistaVerde" /></td>
        </tr>
    </tbody>
</table>
<table style="border: none">
    <thead>
        <tr class="color-cel">
            <th style="width: 33%">EXISTENCIAS</th>
            <th style="width: 33%">BODEGA</th>
            <th style="width: 33%">CONSULTADO EL</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $fecha }} {{ $hora }}</td>
            <td>TODAS</td>
            <td>{{ now() }}</td>
        </tr>
    </tbody>
</table>
<table>
    <thead>
        <tr class="color-cel">
            <th style="width: 5%">#</th>
            <th style="width: 30%">INSUMOS</th>
            <th style="width: fit-content">UNIDAD</th>
            @foreach ($bodegas as $i => $item)
                <th>{{ substr($item->descripcion, 0, 3) }}</th>
            @endforeach
            <th>U.COMPRA</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($articulos as $articulo)
            <tr>
                <td style="width: 5%">{{ $articulo['clave'] }}</td>
                <td style="width: 30%">{{ $articulo['descripcion'] }}</td>
                <td>{{ $articulo['unidad']['descripcion'] }}</td>
                @foreach ($bodegas as $item)
                    <td>{{ $articulo[$item->clave] }}</td>
                @endforeach
                <td>{{ $articulo['ultima_compra'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
