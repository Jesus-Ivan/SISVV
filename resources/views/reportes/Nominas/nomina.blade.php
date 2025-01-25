<style>
    p {
        margin-top: 2pt;
        margin-bottom: 2pt;
    }
</style>
<div style="height: 142pt">
    <table style="width: 100%">
        <tbody>
            <tr>
                <td style="width: 8%"># {{ $nomina->folio }}</td>
                <td style="width: 80%; font-style: bold">{{ $nomina->nombre }}</td>
                <td style="width:fit-content; text-align: right">VVCC</td>
            </tr>
            <tr>
                <td></td>
                <td>{{ $nomina->area }}</td>
                <td style="text-align: right">{{ substr($periodo->created_at, 0, 10) }}</td>
            </tr>
        </tbody>
    </table>

    @if ($nomina->fecha_inicio && $nomina->fecha_fin)
        <table style="width: 100%">
            <tbody>
                <tr>
                    <td>PERIODO DEL: {{ $nomina->fecha_inicio }} AL: {{ $nomina->fecha_fin }}</td>
                    <td style="text-align: right;">
                        ${{ number_format($nomina->diferencia_efectivo + $nomina->extras, 2) }}</td>
                </tr>
                <tr>
                    <td>{{ $nomina->observaciones }}</td>
                    <td style="text-align: right;">{{ $nomina->descuento ? '$' . $nomina->descuento : '' }}</td>
                </tr>
            </tbody>
        </table>
    @endif
    <p style="text-align: center"></p>
    <div style="margin-top: 5pt">
        <table style="width: 100%">
            <tbody>
                <tr>
                    <td style="text-align: right; font-style: bold">
                        ${{ number_format($nomina->diferencia_efectivo + $nomina->extras - $nomina->descuento, 2) }}
                    </td>
                </tr>
                <tr>
                    <td style="border-top-color: black; border-top-width: 2px; border-top-style: solid">NOMBRE Y FIRMA
                        DEL EMPLEADO</td>
                </tr>
            </tbody>
        </table>
    </div>

</div>
