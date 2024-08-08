<style>
    html {
        font-size: x-small
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
    a{
        text-decoration: none;
        color: black;
        font-weight: bold;
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
        <h3>Reporte de cobranza del : {{ $header['fInicio'] }} - {{ $header['fFin'] }}</h3>
        <p>RFC: {{ $header['rfc'] }}</p>
        <p>{{ $header['direccion'] }}</p>
        <p>Tel: 238{{ $header['telefono'] }}</p>
        <hr>
        <p>Consultado el: {{ now() }}</p>
        <p>Cobros del usuario: {{ $header['usuarioCorte'] ? $header['usuarioCorte']->name : 'TODOS' }}</p>
    </div>
    <hr>
    @foreach ($detalles_recibo as $index => $cat)
        <p style="font-weight: 900; font-size: large">{{ $index }}</p>
        <table>
            <thead>
                <tr>
                    <th style="width:10%">Recibo</th>
                    <th style="width:20%">Fecha</th>
                    <th style="width:55%">Nombre</th>
                    <th style="width:12%">Importe</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cat as $detalle_cat)
                    <tr style="{{ $detalle_cat['facturado'] ? 'background-color: gray' : '' }}">
                        <td>
                            <a href="http://localhost:8000/recepcion/cobros/recibo/{{ $detalle_cat['folio'] }}">
                                {{ $detalle_cat['folio'] }}
                            </a>
                        </td>
                        <td>{{ $detalle_cat['created_at'] }}</td>
                        <td>{{ $detalle_cat['nombre'] }}</td>
                        <td>{{ $detalle_cat['monto_pago'] }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td></td>
                    <td></td>
                    <td style="text-align: right">Subtotal: </td>
                    <td>$ {{ array_sum(array_column($cat, 'monto_pago')) }}</td>
                </tr>
            </tfoot>
        </table>
    @endforeach
    <br>
    <p style="font-size: larger">Total: ${{ $total }}</p>
</div>
