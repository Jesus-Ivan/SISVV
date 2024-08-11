<!-- Modal content -->
<div>
    <!-- Result table-->
    <div class="overflow-y-auto h-96 shadow-md sm:rounded-lg">
        <table class="text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="p-4">
                    </th>
                    <th scope="col" class="px-6 py-3 w-fit">
                        REFERENCIA
                    </th>
                    <th scope="col" class="px-6 py-3 w-3/6">
                        CONCEPTO
                    </th>
                    <th scope="col" class="px-6 py-3">
                        CARGO
                    </th>
                    <th scope="col" class="px-6 py-3">
                        ABONO
                    </th>
                    <th scope="col" class="px-6 py-3">
                        SALDO
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->listaCargos as $index => $cargo)
                    <tr wire:key={{ $index }}
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="w-4 p-4">
                            <div class="flex items-center">
                                <input id="checkbox-table-search-{{ $cargo->id }}" type="checkbox"
                                    wire:model.live="cargosSeleccionados.{{ $cargo->id }}"
                                    class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="checkbox-table-search-{{ $cargo->id }}" class="sr-only">checkbox</label>
                            </div>
                        </td>
                        <th scope="row"
                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $cargo->id }}
                        </th>
                        <td class="px-6 py-4">
                            {{ $cargo->concepto }}
                        </td>
                        <td class="px-6 py-4">
                            ${{ $cargo->cargo }}
                        </td>
                        <td class="px-6 py-4">
                            ${{ $cargo->abono }}
                        </td>
                        <td class="px-6 py-4">
                            ${{ $cargo->saldo }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- Botones y total -->
    <div class="flex items-center mt-3 border-gray-200 rounded-b dark:border-gray-600">
        <div class="flex grow">
            <button type="button" wire:click='finishSelect'
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Agregar
                concepto(s)
            </button>
        </div>
        <p>Saldo total: ${{ $totalSeleccionado }}</p>
    </div>
</div>
