<style>
    html {
        font-size: x-small;
    }

    h1,
    h2,
    h3 {
        text-align: center;
    }

    table {
        width: 100%
    }

    thead {
        border-top: 1px solid black;
        border-bottom: 1px solid black;
    }

    tfoot {
        border-top: 1px solid black;
    }

    th {
        text-align: start
    }
</style>
<div>
    <div>
        <h2 style="line-height: 2pt">VISTA VERDE COUNTRY CLUB</h2>
        <h3>Estado de cuenta del: {{$fInicio}} - {{$fFin}}</h3>
        <p style="line-height: 2pt">RFC: {{ $header['rfc'] }}</p>
        <p style="line-height: 2pt">{{ $header['direccion'] }}</p>
        <p style="line-height: 2pt">Tel: 238{{ $header['telefono'] }}</p>
    </div>
    <hr>
    <div>
        <table>
            <tbody>
                <tr>
                    <td>Socio: </td>
                    <td>{{ $resultSocio->id }}-{{ $resultSocio->nombre }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div>
        <table>
            <thead>
                <tr>
                    <th style="width: 70pt">Fecha</th>
                    <th style="width: 55%">Concepto</th>
                    <th>Cargo</th>
                    <th>Abono</th>
                    <th>Saldo</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($resulEstado as $cargo)
                    <tr>
                        <td>{{ $cargo->fecha }}</td>
                        <td style=" border-left-width: 1px; border-left-style: solid; border-left-color: black;">
                            {{ $cargo->concepto }}
                        </td>
                        <td style=" border-left-width: 1px; border-left-style: solid; border-left-color: black;">
                            {{ $cargo->cargo }}
                        </td>
                        <td style=" border-left-width: 1px; border-left-style: solid; border-left-color: black;">
                            {{ $cargo->abono }}
                        </td>
                        <td style=" border-left-width: 1px; border-left-style: solid; border-left-color: black;">
                            {{ $cargo->saldo }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td></td>
                    <td style="text-align: right;">Total:</td>
                    <td>${{ array_sum(array_column($resulEstado->toArray(), 'cargo')) }}</td>
                    <td>${{ array_sum(array_column($resulEstado->toArray(), 'abono')) }}</td>
                    <td>${{ array_sum(array_column($resulEstado->toArray(), 'saldo')) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
