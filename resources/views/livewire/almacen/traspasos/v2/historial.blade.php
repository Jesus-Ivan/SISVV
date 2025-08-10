<div class="ms-3 mx-3">
    <div class="flex py-2 gap-3">
        {{-- FECHA --}}
        <div>
            <input type="date"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
        </div>
        {{-- BODEGA DE ORIGEN --}}
        <div>
            <select id="origen"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option selected value="{{ null }}">BODEGA ORIGEN</option>
            </select>
        </div>
        {{-- BODEGA DE DESTINO --}}
        <div>
            <select id="destino"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option selected value="{{ null }}">BODEGA DESTINO</option>
            </select>
        </div>
        {{-- BOTÓN Y BARRA DE BUSQUEDA --}}
        <div class="flex">
            <div class="relative">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                    </svg>
                </div>
                <input type="text"
                    class="w-96 p-2.5 me-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Código o Descripción" />
            </div>
            <div>
                <button type="submit"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    <svg wire:loading.remove wire:target='buscar' class="w-5 h-5" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                            d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                    </svg>
                    <span class="sr-only">Buscar</span>
                </button>
            </div>
        </div>
    </div>

    {{-- TABLA DE RESULTADOS --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg my-2">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        TRASPASO
                    </th>
                    <th scope="col" class="px-6 py-3">
                        FECHA EXISTENCIAS
                    </th>
                    <th scope="col" class="px-6 py-3">
                        #
                    </th>
                    <th scope="col" class="px-6 py-3">
                        PRESENTACIÓN / INSUMO
                    </th>
                    <th scope="col" class="px-6 py-3">
                        CANTIDAD
                    </th>
                    <th scope="col" class="px-6 py-3">
                        COSTO U.
                    </th>
                    <th scope="col" class="px-6 py-3">
                        IVA
                    </th>
                    <th scope="col" class="px-6 py-3">
                        C. C. IMPUESTO
                    </th>
                    <th scope="col" class="px-6 py-3">
                        IMPORTE
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                    <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        10
                    </th>
                    <td class="px-6 py-2">
                        2025-08-08 20:00
                    </td>
                    <td class="px-6 py-2">
                        1234
                    </td>
                    <td class="px-6 py-2 w-80">
                        GARRAFON AGUA CIEL 20L
                    </td>
                    <td class="px-6 py-2">
                        5
                    </td>
                    <td class="px-6 py-2">
                        $14.50
                    </td>
                    <td class="px-6 py-2">
                        16 %
                    </td>
                    <td class="px-6 py-2">
                        $16.62
                    </td>
                    <td class="px-6 py-2">
                        $50.46
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- BOTON DE REGRESAR --}}
    <a type="button" href="{{ route('almacen.traspasov2') }}"
        class="my-2 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
        <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
            width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M5 12h14M5 12l4-4m-4 4 4 4" />
        </svg>

        Regresar
    </a>
</div>
