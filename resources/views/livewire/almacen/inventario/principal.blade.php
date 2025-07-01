<div class="ms-3 mx-3">
    <div class="flex gap-2 items-end">
        {{-- FECHA MES --}}
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Buscar por Mes</label>
            <input datepicker type="month"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        </div>
        {{-- BODEGA --}}
        <form class="w-fit">
            <select id="bodega"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option selected>SELECCIONAR BODEGA</option>
                <option value="US">ALMACÉN</option>
                <option value="CA">CAFETERIA</option>
                <option value="FR">CADDIE BAR</option>
                <option value="DE">RESTAURANT</option>
            </select>
        </form>
        {{-- BOTON DE BUSQUEDA --}}
        <button type="button"
            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            <svg wire:loading.remove wire:target='buscar' class="w-5 h-5" aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                    d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
            </svg>
            <!--Loading indicator-->
            <div wire:loading wire:target='buscar'>
                @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
            </div>
            <span class="sr-only">Buscar</span>
        </button>
    </div>

    {{-- TABLA DE INVENTARIOS --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg my-2">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        FOLIO
                    </th>
                    <th scope="col" class="px-6 py-3">
                        FECHA INVENTARIO
                    </th>
                    <th scope="col" class="px-6 py-3">
                        HORA INVENTARIO
                    </th>
                    <th scope="col" class="px-6 py-3">
                        BODEGA
                    </th>
                    <th scope="col" class="px-6 py-3">
                        OBSERVACIONES
                    </th>
                    <th scope="col" class="px-6 py-3">

                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                    <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        1
                    </th>
                    <td class="px-6 py-2">
                        20/02/2025
                    </td>
                    <td class="px-6 py-2">
                        10:20:30
                    </td>
                    <td class="px-6 py-2">
                        ALMACÉN
                    </td>
                    <td class="px-6 py-2">
                        AJUSTE DIADIO POR DIFERENCIAS
                    </td>
                    <td class="px-6 py-2 text-center">
                        <button type="button"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Detalles</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
