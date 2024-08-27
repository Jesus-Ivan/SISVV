<style>
    html {
        font-size: small
    }

    h1,
    h2,
    h3,
    h4 {
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
        @if ($consumosMesFin)
            <h3>Cuentas por cobrar</h3>
        @else
            <h3>Cartera de clientes vencidos</h3>
        @endif
        <p>RFC: {{ $header['rfc'] }}</p>
        <p>{{ $header['direccion'] }}</p>
        <p>Tel: 238{{ $header['telefono'] }}</p>
        <p>Consultado el: {{ now() }}</p>
    </div>
    <hr>
    <table>
        <thead>
            <tr>
                <th style="width: 12%">No. Socio</th>
                <th style="width: 70%">Nombre</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($totales as $item)
                <tr>
                    <td>{{ $item['id_socio'] }}</td>
                    <td>{{ $item['nombre'] }}</td>
                    <td>${{ $item['monto'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <br>
    <p style="font-size: larger">Total: ${{ $total }}</p>
</div>
