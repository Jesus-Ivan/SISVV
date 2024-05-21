<!-- Modal content -->
<div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
    <!-- Modal header -->
    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
            Resumen estado de cuenta
        </h3>
        <button type="button"
            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
            data-modal-hide="modal-cargos">
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
            </svg>
            <span class="sr-only">Close modal</span>
        </button>
    </div>
    <!-- Modal body -->
    <div class="p-4 md:p-5 space-y-4">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <!-- Result table-->
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="p-4">
                        </th>
                        <th scope="col" class="px-6 py-3 w-fit">
                            REFERENCIA
                        </th>
                        <th scope="col" class="px-6 py-3 w-full">
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
                                        wire:model="cargosSeleccionados.{{ $cargo->id }}"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="checkbox-table-search-1" class="sr-only">checkbox</label>
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
    </div>
    <!-- Modal footer -->
    <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
        <button data-modal-hide="modal-cargos" type="button" wire:click='finishSelect'
            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Agregar
            concepto(s)</button>
    </div>
</div>
