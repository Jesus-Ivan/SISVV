<div class="relative overflow-x-auto shadow-md sm:rounded-lg my-2">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-3 py-3">
                    #
                </th>
                <th scope="col" class="px-3 py-3">
                    PRESENTACIÓN
                </th>
                <th scope="col" class="px-3 py-3">
                    C. CON IVA
                </th>
                <th scope="col" class="px-3 py-3">
                    EXISTENCIA TEÓRICA
                </th>
                <th scope="col" class="px-3 py-3">
                    EXISTENCIA REAL
                </th>
                <th scope="col" class="px-3 py-3">
                    DIFERENCIA
                </th>
                <th scope="col" class="px-3 py-3">
                    DIF. IMPORTE
                </th>
                <th scope="col" class="px-3 py-3">
                    CONCEPTO
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($table as $index => $item)
                <tr wire:key ='{{ $index }}'
                    class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                    <th scope="row" class="px-3 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $item['clave'] }}
                    </th>
                    <td class="px-3 py-2 w-96">
                        {{ $item['descripcion'] }}
                    </td>
                    <td class="px-3 py-2">
                        $ {{ $item['costo_con_impuesto'] }}
                    </td>
                    <td class="px-3 py-2">
                        {{ $item['existencias_presentacion'] }}
                    </td>
                    <td class="px-3 py-2">
                        <input type="number" wire:model='table.{{ $index }}.existencias_real'
                            wire:change='actualizarReal({{ $index }}, true)'
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-28 p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                    </td>
                    <td class="px-3 py-2">
                        {{ $item['diferencia'] }}
                    </td>
                    <td class="px-3 py-2">
                        ${{ $item['diferencia_importe'] }}
                    </td>
                    <td class="px-3 py-2">
                        <select wire:model='table.{{ $index }}.clave_concepto'
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option selected value="{{ null }}">Concepto ajuste</option>
                            @foreach ($this->conceptos as $item)
                                <option value="{{ $item->clave }}">{{ $item->descripcion }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
