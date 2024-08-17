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

    a {
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
        <h3>Reporte de recibos del socio: {{ $id_socio }}</h3>
        <hr>
        <p>Consultado el: {{ now() }}</p>
    </div>
    <hr>

    <table>
        <thead>
            <tr>
                <th style="width:7%">Recibo</th>
                <th style="width:20%">Fecha</th>
                <th style="width:55%">Nombre</th>
                <th>Usuario</th>
                <th style="width:12%">Total</th>
            </tr>
        </thead>
        @foreach ($recibos as $index => $recibo)
            <tbody>
                <tr style="{{ $recibo['facturado'] ? 'background-color: gray' : '' }}">
                    <td>
                        <a href="http://localhost:8000/recepcion/cobros/recibo/{{ $recibo['folio'] }}">
                            {{ $recibo['folio'] }}
                        </a>
                    </td>
                    <td>{{ $recibo['created_at'] }}</td>
                    <td>{{ $recibo['nombre'] }}</td>
                    <td>{{ $recibo['caja']['users']->name }}</td>
                    <td>{{ $recibo['total'] }}</td>
                </tr>
            </tbody>
        @endforeach
    </table>

</div>
