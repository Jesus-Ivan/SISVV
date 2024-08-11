<table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400" wire:loading.class="opacity-50"
    wire:target='finishSelect, aplicarMetodosPago'>
    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th scope="col" class="px-6 py-3 w-32">
                REFERENCIA
            </th>
            <th scope="col" class="px-6 py-3 ">
                CONCEPTO
            </th>
            <th scope="col" class="px-6 py-3 w-64">
                METODO PAGO
            </th>
            <th scope="col" class="px-6 py-3 w-32">
                SALDO
            </th>
            <th scope="col" class="px-6 py-3 w-32">
                ABONO
            </th>
            <th scope="col" class="px-6 py-3 w-32">
                ACCIONES
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($cargosTabla as $index => $item)
            <tr wire:key="{{ $index }}"
                class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $item['id'] }}
                </th>
                <td class="px-6 py-4 ">
                    {{ $item['concepto'] }}
                </td>
                <td scope="col" class="px-6 py-3">
                    <select wire:model="cargosTabla.{{ $index }}.id_tipo_pago"
                        wire:loading.attr="disabled"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="{{ null }}" selected>Seleccione</option>
                        @foreach ($this->listaPagos as $pago)
                            <option value="{{ $pago->id }}">{{ $pago->descripcion }}</option>
                        @endforeach
                    </select>
                    @error('cargosTabla.' . $index . '.id_tipo_pago')
                        <x-input-error messages="{{ $message }}" />
                    @enderror
                </td>
                <td class="px-6 py-4">
                    ${{ $item['saldo_anterior'] }}
                </td>
                <td class="px-6 py-4">
                    <div class="inline-flex items-center">
                        $
                        <input type="number" wire:model="cargosTabla.{{ $index }}.monto_pago"
                            wire:loading.attr="disabled" wire:change="calcularTotales()"
                            class="min-w-20 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="0" />
                    </div>
                    @error('cargosTabla.' . $index . '.monto_pago')
                        <x-input-error messages="{{ $message }}" />
                    @enderror
                </td>
                <td class="px-6 py-4 ">
                    <button type="button" wire:click='removerCargo({{ $index }})' wire:loading.attr="disabled"
                        class="text-red-700 border border-red-700 hover:bg-red-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:focus:ring-red-800 dark:hover:bg-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                            <path fill-rule="evenodd"
                                d="M8.586 2.586A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h3a1 1 0 1 1 0 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8a1 1 0 0 1 0-2h3V4a2 2 0 0 1 .586-1.414ZM10 6h4V4h-4v2Zm1 4a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Zm4 0a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="sr-only">Borrar</span>
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="font-semibold text-gray-900 dark:text-white">
            <th scope="row" class="px-6 py-3 text-base"></th>
            <td class="px-6 py-3"></td>
            <td class="px-6 py-3">Total</td>
            <td class="px-6 py-3">${{ $totalSaldo }}</td>
            <td class="px-6 py-3">${{ $totalAbono }}</td>
        </tr>
        <tr class="font-semibold text-gray-900 dark:text-white">
            <th scope="row" class="px-6 py-3 text-base"></th>
            <td class="px-6 py-3"></td>
            <td class="px-6 py-3">Saldo a favor generado</td>
            <td class="px-6 py-3"></td>
            <td class="px-6 py-3">${{ $saldoFavor }}</td>
        </tr>
    </tfoot>
</table>
