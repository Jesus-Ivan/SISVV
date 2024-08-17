<style>
    html {
        font-size: x-small;
        margin: 12pt;
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
        font-style: normal,
            font-weight: bold,
    }

    .noremarcardo {
        font-style: normal,
            font-weight: normal;
    }

    .cuerpoTabla {
        min-height: 100vh;
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
                    <img src="<?php echo $base64; ?>" height="47" alt="logoVistaVerde" />
                </td>
                <td style="width: 100%">
                    <div>
                        <p style="color: green; font-size: 28pt; font-weight: bolder; text-align: center">RECIBO DE CAJA
                        </p>
                    </div>
                </td>
                <td style="display: flex ;align-items: flex-start">
                    <div
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
                    <div
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
                    <div
                        style="text-align: center; padding: 2pt; width: 105pt; font-weight: bolder; background: green; color: white">
                        <p>FECHA</p>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <table>
        <tbody>
            <tr>
                <td style="width: 85%">
                    <div style="font-size: 14pt">
                        {{ $cobro->id_socio }} - {{ $cobro->nombre }}
                    </div>
                </td>
                <td>
                    {{ $cobro->created_at }}
                </td>
            </tr>
        </tbody>
    </table>
    <br>
    {{-- TABLE CARGOS --}}
    <table style= "margin-top: 6pt; margin-bottom: 6pt; height: 190pt;">
        <thead>
            <tr style="background: green">
                <th style="width: 45%">CONCEPTO</th>
                <th>METODO PAGO</th>
                <th>SALDO ANTERIOR</th>
                <th style="width: 10%">ABONO</th>
                <th style="width: 10%">SALDO</th>
            </tr>
        </thead>
        <tbody class="cuerpoTabla">
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
        <tfoot style="height: 32pt">
            <tr>
                <td></td>
                <td></td>
                <td style="font-weight: bolder">TOTAL: </td>
                <td style="border-top: 2px; border-top-style: solid; border-top-color: black; font-weight: bolder">
                    ${{ array_sum(array_column($detalles->toArray(), 'monto_pago')) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    {{-- Footer del recibo --}}
    <table>
        <thead>
            <tr style="color: white; font-weight: bolder">
                <td style="background: green; padding: 2pt; text-align: center">CANTIDAD CON LETRA</td>
                <td style="width:50%"></td>
                <td style="background: green; padding: 2pt; text-align: center">CAJA</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td></td>
                <td></td>
                <td>
                    <div>
                        <p>{{ $cobro->folio }}</p>
                        <p>{{ now() }}</p>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <div>
        <p style="text-align: center; font-weight: bolder; color: green">"EVITE RECARGOS, PAGUE ANTES DEL DÍA 10 DE CADA
            MES"</p>
    </div>
    @if ($saldoFavor)
        <p class="remarcardo">SALDO A FAVOR GENERADO: <span class="noremarcardo">${{ $saldoFavor->saldo }}</span></p>
    @endif
</div>
