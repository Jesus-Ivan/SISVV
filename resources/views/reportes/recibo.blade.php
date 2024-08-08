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
            font-weight: bold,
    }

    .noremarcardo {
        font-style: normal,
            font-weight: normal;
    }
</style>
<div>
    <?php
    $path = 'storage/image001.jpg';
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    ?>
    <div>
        <h2>VISTA VERDE COUNTRY CLUB</h2>
        <table>
            <tbody>
                <tr>
                    <td>
                        <img src="<?php echo $base64; ?>" height="50" alt="logoVistaVerde" />
                    </td>
                    <td>
                        <p>RFC: {{ $header['rfc'] }}</p>
                        <p>{{ $header['direccion'] }}</p>
                        <p>Tel: 238{{ $header['telefono'] }}</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <hr>
    <div>
        <p class="remarcardo" style="font-size: 16px;">RECIBO : <span class="noremarcardo">{{ $cobro->folio }}</span></p>
        <p class="remarcardo" style="font-size: 16px; display: flex;">
            FECHA RECIBO : <span class="noremarcardo" style="margin-right:18%">{{ $cobro->created_at }}</span>
            CONSULTADO : <span class="noremarcardo">{{ now() }}</span>
        </p>
        <p class="remarcardo" style="font-size: 16px;">SOCIO : <span class="noremarcardo">{{ $cobro->id_socio }} -
                {{ $cobro->nombre }}</span></p>
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
    <p class="remarcardo" style="font-size: 16px;">ABONO TOTAL : <span
            class="noremarcardo">${{ array_sum(array_column($detalles->toArray(), 'monto_pago')) }}</span>
    </p>
    @if ($saldoFavor)
        <p class="remarcardo">SALDO A FAVOR GENERADO: <span class="noremarcardo">${{ $saldoFavor->saldo }}</span></p>
    @endif
</div>
