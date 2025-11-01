<div class="relative overflow-x-auto shadow-md sm:rounded-lg my-2">
    <table wire:loading.remove wire:target='limpiarExistencias, conceptoGeneral'
        class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-3 py-3">
                    #
                </th>
                <th scope="col" class="px-3 py-3">
                    INSUMO
                </th>
                <th scope="col" class="px-3 py-3">
                    C. CON IVA
                </th>
                <th scope="col" class="px-3 py-3">
                    EXISTENCIA TEÓRICA
                </th>
                <th scope="col" class="px-3 py-3 flex">
                    <span class="flex-grow">
                        EXISTENCIA REAL
                    </span>
                    <x-dropdown>
                        <x-slot name="trigger">
                            <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-width="4"
                                    d="M12 6h.01M12 12h.01M12 18h.01" />
                            </svg>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link wire:click="limpiarExistencias">Limpiar existencias</x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                </th>
                <th scope="col" class="px-3 py-3">
                    DIFERENCIA
                </th>
                <th scope="col" class="px-3 py-3">
                    UNIDAD INSUMO
                </th>
                <th scope="col" class="px-3 py-3">
                    DIF. IMPORTE
                </th>
                <th scope="col" class="px-3 py-3 flex">
                    <span class="flex-grow">
                        CONCEPTO
                    </span>
                    <x-dropdown>
                        <x-slot name="trigger">
                            <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-width="4"
                                    d="M12 6h.01M12 12h.01M12 18h.01" />
                            </svg>
                        </x-slot>
                        <x-slot name="content">
                            @foreach ($this->conceptos as $item)
                                <x-dropdown-link
                                    wire:click="conceptoGeneral('{{ $item->clave }}')">{{ $item->descripcion }}</x-dropdown-link>
                            @endforeach
                        </x-slot>
                    </x-dropdown>
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
                        {{ $item['existencias_insumo'] }}
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
                        {{ $item['unidad_descripcion'] }}
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
    <div class="w-full" wire:loading wire:target='limpiarExistencias, conceptoGeneral'>
        <div
            class="flex items-center justify-center h-56 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
            <div
                class="w-fit px-3 py-1 text-base font-semibold leading-none text-center text-blue-800 bg-blue-200 rounded-full animate-pulse dark:bg-blue-900 dark:text-blue-200">
                <p>Ajustando valores ...</p>
            </div>
        </div>
    </div>
</div>
