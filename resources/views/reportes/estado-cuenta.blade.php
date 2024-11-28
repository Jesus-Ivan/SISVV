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
        <h3>Estado de cuenta del: {{ $fInicio }} - {{ $fFin }}</h3>
        <p style="line-height: 2pt">RFC: {{ $header['rfc'] }}</p>
        <p style="line-height: 2pt">{{ $header['direccion'] }}</p>
        <p style="line-height: 2pt">Tel: 238{{ $header['telefono'] }}</p>
    </div>
    <hr>
    <div>
        <table>
            <tbody>
                <tr>
                    <td style="width: 70pt; font-weight: bold">Socio: </td>
                    <td>
                        {{ $resultSocio->id }}-{{ $resultSocio->nombre . ' ' . $resultSocio->apellido_p . ' ' . $resultSocio->apellido_m }}
                    </td>
                </tr>
                <tr>
                    <td style="width:70pt; font-weight: bold">Consultado el: </td>
                    <td>
                        {{ now() }}
                    </td>
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
                    <tr style="{{ $cargo->vista != 'ORD' ? 'background-color: aquamarine' : '' }}">
                        <td>{{ $cargo->fecha }}</td>
                        <td style=" border-left-width: 1px; border-left-style: solid; border-left-color: black;">
                            {{ $cargo->concepto }}
                        </td>
                        <td
                            style="text-align: right ; border-left-width: 1px; border-left-style: solid; border-left-color: black;">
                            $ {{ number_format($cargo->cargo, 2) }}
                        </td>
                        <td
                            style="text-align: right ; border-left-width: 1px; border-left-style: solid; border-left-color: black;">
                            $ {{ number_format($cargo->abono, 2) }}
                        </td>
                        <td
                            style="text-align: right ; border-left-width: 1px; border-left-style: solid; border-left-color: black;">
                            $ {{ number_format($cargo->saldo, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td></td>
                    <td style="text-align: right;">Subtotal:</td>
                    <td>$ {{ number_format(array_sum(array_column($resulEstado->toArray(), 'cargo')), 2) }}</td>
                    <td>$ {{ number_format(array_sum(array_column($resulEstado->toArray(), 'abono')), 2) }}</td>
                    <td>$ {{ number_format(array_sum(array_column($resulEstado->toArray(), 'saldo')), 2) }}</td>
                </tr>
                @if (count($saldoFavor) > 0)
                    <tr>
                        <td></td>
                        <td style="text-align: right;">Saldo a favor:</td>
                        <td></td>
                        <td></td>
                        <td>${{ number_format(array_sum(array_column($saldoFavor->toArray(), 'saldo')), 2) }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td style="text-align: right;">Total:</td>
                        <td></td>
                        <td></td>
                        <td>${{ number_format(array_sum(array_column($resulEstado->toArray(), 'saldo')) - array_sum(array_column($saldoFavor->toArray(), 'saldo')), 2) }}
                        </td>
                    </tr>
                @endif
            </tfoot>
        </table>
    </div>
</div>
