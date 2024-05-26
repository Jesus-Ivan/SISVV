<style>
    html {
        font-size: x-small
    }

    h1,
    h2 {
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
        <p>RFC: {{ $header['rfc'] }}</p>
        <p>{{ $header['direccion'] }}</p>
        <p>Tel: 238{{ $header['telefono'] }}</p>
    </div>
    <hr>
    <div>
        <p class="remarcardo">SOCIO : <span class="noremarcardo">{{ $cobro->id_socio }} - {{ $cobro->nombre }}</span></p>
        <p class="remarcardo">RECIBO : <span class="noremarcardo">{{ $cobro->folio }}</span></p>
        <p class="remarcardo">FECHA RECIBO : <span class="noremarcardo">{{ $cobro->fecha }}</span></p>
    </div>
    <br>
    <table>
        <thead>
            <tr>
                <th style="width: 45%">CONCEPTO</th>
                <th>METODO PAGO</th>
                <th>SALDO ANTERIOR</th>
                <th>ABONO</th>
                <th>SALDO</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detalles as $detalle)
                <tr>
                    <td>{{ $detalle->concepto }}</td>
                    <td>{{ $detalle->descripcion }}</td>
                    <td>{{ $detalle->saldo_anterior }}</td>
                    <td>{{ $detalle->monto_pago }}</td>
                    <td>{{ $detalle->saldo }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <hr>
    <p class="remarcardo">OBSERVACIONES : <span class="noremarcardo">{{ $cobro->observaciones }}</span></p>
    <p class="remarcardo">ABONO TOTAL : <span
            class="noremarcardo">${{ array_sum(array_column($detalles->toArray(), 'monto_pago')) }}</span>
    </p>
    @if ($saldoFavor)
        <p class="remarcardo">SALDO A FAVOR GENERADO: <span class="noremarcardo">${{ $saldoFavor->saldo }}</span></p>
    @endif
</div>
