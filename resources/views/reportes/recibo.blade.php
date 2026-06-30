<style>
    html {
        font-size: x-small;
        margin-top: 12pt;
        margin-bottom: 12pt;
        margin-left: 16pt;
        margin-right: 14pt;
    }

    h1,
    h2 {
        text-align: center
    }

    table {
        width: 100%;
    }

    thead {
        border-style: none;
    }

    th {
        text-align: left;
        padding-top: 3px;
        padding-bottom: 3px;
    }

    p {
        margin: 0;
    }

    .remarcardo {
        font-style: normal;
        font-weight: bold;
    }

    .noremarcardo {
        font-style: normal;
        font-weight: normal;
    }

    .opacidad {
        opacity: 0;
    }
</style>

<div>
    <?php
    $path = 'storage/image001.jpg';
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    ?>
    {{-- HEADER --}}
    <table style="margin-bottom: 10pt">
        <tbody>
            <tr>
                <td style="display: flex ;align-items: flex-start">
                    <img src="<?php echo $base64; ?>" height="47" alt="logoVistaVerde" class="opacidad" />
                </td>
                <td style="width: 100%">
                    <div>
                        <p style="color: green; font-size: 28pt; font-weight: bolder; text-align: center"
                            class="opacidad">RECIBO DE CAJA
                        </p>
                    </div>
                </td>
                <td style="display: flex ;align-items: flex-start">
                    <div class="opacidad"
                        style="text-align: center; padding: 2pt; width: 105pt; font-weight: bolder; background: green; color: white">
                        <p>FOLIO</p>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    {{-- RECIBIMOS DE...... FECHA --}}
    <table>
        <tbody>
            <tr>
                <td>
                    <div class="opacidad"
                        style="text-align: center; padding: 2pt; width: 109pt; font-weight: bolder; background: green; color: white">
                        <p>RECIBIMOS DE:</p>
                    </div>
                </td>
                <td style="width:100% ">
                    <div style=" color: white">
                        <p>a</p>
                    </div>
                </td>
                <td>
                    <div class="opacidad"
                        style="text-align: center; padding: 2pt; width: 105pt; font-weight: bolder; background: green; color: white">
                        <p>FECHA</p>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    {{-- Datos del socio y fecha y hora --}}
    <table style="margin-top: 2pt">
        <tbody>
            <tr>
                <td>
                    <div style="font-size: 14pt">
                        {{ $cobro->id_socio }} - {{ $cobro->nombre }}
                    </div>
                </td>
                <td style="width: 19%">
                    {{ $cobro->created_at }}
                </td>
            </tr>
        </tbody>
    </table>
    <br>
    {{-- TABLE CARGOS --}}
    <table style= "margin-bottom: 6pt; height: 190pt;">
        <thead>
            <tr style="background: green; color: white">
                <th class="opacidad" style="width: 50%">CONCEPTO</th>
                <th class="opacidad">METODO PAGO</th>
                <th class="opacidad" style="width: 10%">S.ANTERIOR</th>
                <th class="opacidad" style="width: 10%">ABONO</th>
                <th class="opacidad" style="width: 10%">SALDO</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detalles as $detalle)
                <tr>
                    <td>{{ $detalle->concepto }}</td>
                    <td>{{ $detalle->descripcion }}</td>
                    <td style="text-align: right">$ {{ number_format($detalle->saldo_anterior, 2) }}</td>
                    <td style="text-align: right">$ {{ number_format($detalle->monto_pago, 2) }}</td>
                    <td style="text-align: right">$ {{ number_format($detalle->saldo, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{-- Footer del recibo --}}
    <table>
        <thead>
            <tr style="color: white; font-weight: bolder">
                <td class="opacidad" style="background: green; padding: 2pt; text-align: center">CANTIDAD CON LETRA</td>
                <td style="width:50%; color: black; text-align: center; font-size: 12pt">TOTAL:
                    $ {{ number_format(array_sum(array_column($detalles->toArray(), 'monto_pago')), 2) }}</td>
                <td class="opacidad" style="background: green; padding: 2pt; text-align: center">CAJA</td>
            </tr>
        </thead>
    </table>
    <table>
        <tbody>
            <tr>
                <td style="display: flex; align-items: flex-start">{{ $total_letras }}</td>
                <td style="width: 125pt">
                    <p>{{ $cobro->folio }}</p>
                    <p>{{ now() }}</p>
                    @if ($saldoFavor)
                        <p class="remarcardo">S.FAVOR: <span class="noremarcardo">${{ $saldoFavor->saldo }}</span>
                        </p>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
    <div>
        <p class="opacidad" style="text-align: center; font-weight: bolder; color: green">"EVITE RECARGOS, PAGUE ANTES
            DEL DÍA 10 DE CADA
            MES"</p>
    </div>
</div>
