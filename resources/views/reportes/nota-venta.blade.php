<style>
    html {
        margin: 10pt;
        font-size: 7pt;
    }

    p {
        margin-top: 2pt;
        margin-bottom: 2pt;
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
        line-height:8pt;
        padding: 0pt;
        text-align: left;
    }
</style>
<h3>{{ $title }}</h3>
<p>RFC: {{ $rfc }}</p>
<p>{{ $direccion }}</p>
<p>Tel: 238{{ $telefono }}</p>
<hr>
<table>
    <tbody>
        <tr>
            <td style="width: 20mm">Folio: {{ $folio }}</td>
            <td >Fecha: {{ $fecha }}</td>
        </tr>
        <tr>
            <td>Socio: {{ $socio_id }}</td>
            <td>{{ $socio_nombre }}</td>
        </tr>
        <tr>
            <td>Vendedor:</td>
            <td>{{ count($caja) > 0 ? $caja[0]->users->name : 'ERR' }}</td>
        </tr>
        <tr>
            <td>Punto:</td>
            <td>{{ count($caja) > 0 ? $caja[0]->puntoVenta->nombre : 'ERRPV' }}</td>
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
<h4>DETALLES DE PAGO</h4>
<table>
    <thead>
        <tr>
            <th style="width: 40mm">Socio</th>
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
