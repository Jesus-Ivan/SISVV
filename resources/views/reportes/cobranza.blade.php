<style>
    html {
        font-size: small
    }

    h1,
    h2,
    h3 {
        text-align: center
    }

    table {
        width: 100%
    }

    thead {
        border-top: 1px solid black;
        border-bottom: 1px solid black;
    }

    th {
        text-align: left;
        padding-top: 2px;
        padding-bottom: 2px;
    }

    tfoot {
        border-top: 1px solid black;
    }

    p {
        margin: 0;
    }

    .remarcardo {
        font-style: normal,
            font-weight: bold;
    }

    .noremarcardo {
        font-style: normal,
            font-weight: normal;
    }
</style>
<div>
    <div>
        <h2>VISTA VERDE COUNTRY CLUB</h2>
        <h3>Reporte de cobranza</h3>
        <p>RFC: {{ $header['rfc'] }}</p>
        <p>{{ $header['direccion'] }}</p>
        <p>Tel: 238{{ $header['telefono'] }}</p>
    </div>
    <hr>
    @foreach ($detalles_recibo as $index => $cat)
        <p>{{ $index }}</p>
        <table>
            <thead>
                <tr>
                    <th style="width:15%">Fecha</th>
                    <th style="width:75%">Nombre</th>
                    <th>Importe</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cat as $detalle_cat)
                    <tr>
                        <td>{{ substr($detalle_cat->fecha, 0, 10) }}</td>
                        <td>{{ $detalle_cat->nombre }}</td>
                        <td>{{ $detalle_cat->monto_pago }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td></td>
                    <td style="text-align: right">Subtotal: </td>
                    <td>$ {{ array_sum(array_column($cat->toArray(), 'monto_pago')) }}</td>
                </tr>
            </tfoot>
        </table>
    @endforeach
    <br>
    <p>Total: ${{ $total }}</p>
</div>
