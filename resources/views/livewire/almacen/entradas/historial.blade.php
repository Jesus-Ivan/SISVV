<div class="ms-3 mx-3">
    {{-- Search bar --}}
    <div class="flex gap-4 items-end">
        {{-- Fecha inicio --}}
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Fecha de inicio</label>
            <input datepicker type="date" datepicker-format="dd/mm/yyyy"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-36 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Seleccionar fecha">
        </div>
        {{-- Fecha fin --}}
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Fecha de fin</label>
            <input datepicker type="date" datepicker-format="dd/mm/yyyy"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-36 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Seleccionar fecha">
        </div>
        {{-- Campo de entrada --}}
        <div class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between">
            <label for="table-search" class="sr-only">Buscar</label>
            <div class="relative">
                <div
                    class="absolute inset-y-0 left-0 rtl:inset-r-0 rtl:right-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="currentColor"
                        viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                            clip-rule="evenodd"></path>
                    </svg>
                </div>
                <input type="text" id="table-search"
                    class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Buscar">
            </div>
        </div>
    </div>
    {{-- Tabla --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-2">
                        FOLIO ENTRADA
                    </th>
                    <th scope="col" class="px-6 py-2">
                        FECHA ENTRADA
                    </th>
                    <th scope="col" class="px-6 py-2">
                        CÓDIGO
                    </th>
                    <th scope="col" class="px-4 py-2">
                        DESCRIPCIÓN
                    </th>
                    <th scope="col" class="px-4 py-2">
                        CANTIDAD
                    </th>
                    <th scope="col" class="px-4 py-2">
                        COSTO UNITARIO
                    </th>
                    <th scope="col" class="px-4 py-2">
                        TOTAL
                    </th>
                    <th scope="col" class="px-6 py-2">
                        IVA
                    </th>
                    <th scope="col" class="px-6 py-2">
                        FECHA COMPRA
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->detalles as $index => $detalle)
                    <tr wire:key='{{ $index }}'
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-2">
                            {{ $detalle->folio_entrada }}
                        </td>
                        <td class="px-6 py-2">
                            {{ $detalle->created_at }}
                        </td>
                        <td class="px-6 py-2">
                            {{ $detalle->codigo_producto }}
                        </td>
                        <td class="px-6 py-2">
                            {{ $detalle->nombre }}
                        </td>
                        <td class="px-6 py-2">
                            {{ $detalle->cantidad }}
                        </td>
                        <td class="px-6 py-2">
                            ${{ $detalle->costo_unitario }}
                        </td>
                        <td class="px-6 py-2">
                            ${{ $detalle->importe }}
                        </td>
                        <td class="px-6 py-2">
                            ${{ $detalle->iva }}
                        </td>
                        <td class="px-6 py-2">
                            {{ $detalle->fecha_compra }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{-- PAGINADOR --}}
    <div>
        {{ $this->detalles->links() }}
    </div>
</div>
