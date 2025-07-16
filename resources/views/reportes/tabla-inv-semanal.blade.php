<style>
    html {
        font-size: x-small;
        margin: 16pt;
    }

    p {
        text-transform: uppercase;
        margin: 0px
    }

    tr,
    td {
        border: 1pt;
        border-top-style: solid;
        border-top-color: black;
    }

    .padding-cel {
        padding: 2pt;
    }

    .color-cel {
        background-color: #cacaca;
    }

    .sub-cell {
        border: 1pt;
        border-left-style: solid;
        border-left-color: black;
        height: 6pt;
        max-height: 12pt;
    }
</style>

<div>
    {{-- Tabla padre --}}
    <table style="width: 100%;">
        <thead>
            <tr>
                <th>{{ $bodega->descripcion }}</th>
                @foreach ($fechas as $fecha)
                    <td style="text-transform: uppercase; text-align: center">
                        <p style="font-weight: bold">{{ $fecha->locale('es')->shortDayName }}
                            {{ $fecha->toDateString() }}</p>
                        <table style="width: 100%">
                            <tr>
                                <td class="sub-cell">E</td>
                                <td class="sub-cell">P</td>
                                <td class="sub-cell">R</td>
                            </tr>
                        </table>
                    </td>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($articulos as $item)
                <tr class="{{ $loop->odd ? 'color-cel' : '' }}">
                    <td style="height: fit-content">{{ $item->descripcion }}</td>
                    @foreach ($fechas as $fecha)
                        <td style="width: 14%">
                            <table style="width: 100%">
                                <tr>
                                    <td class="sub-cell"></td>
                                    <td class="sub-cell"></td>
                                    <td class="sub-cell"></td>
                                </tr>
                            </table>
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
