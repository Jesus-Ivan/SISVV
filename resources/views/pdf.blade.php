<style>
    html {
        margin: 10pt;
    }
</style>
<h4>{{ $title }}</h4>
<p>RFC: {{ $rfc }}</p>
<p>{{ $direccion }}</p>
<p>Tel: 238{{ $telefono }}</p>
<table>
    <thead>
        <tr>
            <th>Folio: {{ $folio }}</th>
            <th>Fecha: {{ $fecha }}</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $socio_id }}</td>
            <td>{{ $socio_nombre }}</td>
        </tr>
    </tbody>
</table>
<table>
    <thead>
        <tr>
            <th>Descripcion</th>
            <th>Cantidad</th>
            <th>Precio</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($productos as $producto)
            <tr>
                <td>{{$producto->codigo_venta_producto}}</td>
                <td>{{$producto->cantidad}}</td>
                <td>{{$producto->precio}}</td>
                <td>{{$producto->subtotal}}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td>{{$total}}</td>
        </tr>
    </tfoot>
</table>
<table>
    <thead>
        <tr>
            <th>Socio</th>
            <th>Monto</th>
            <th>T.Pago</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($pagos as $pago)
            <tr>
                <td>{{$pago->nombre}}</td>
                <td>{{$pago->monto}}</td>
                <td>{{$pago->id_tipo_pago}}</td>
            </tr>
        @endforeach
    </tbody>
</table>

