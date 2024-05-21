<div>
    {{-- Filtro de busqueda --}}
    <div class="relative ms-3 w-96">
        <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                fill="none" viewBox="0 0 20 20">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
            </svg>
        </div>
        <input wire:model.live.debounce.500ms="search" type="search" id="search"
            class="block w-full p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            placeholder="Buscar nombre de artículo o código" required />
    </div>

    {{-- Tabla --}}
    <div class="ms-3 mx-3 my-2">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            CÓDIGO
                        </th>
                        <th scope="col" class="px-6 py-3">
                            TIPO
                        </th>
                        <th scope="col" class="px-20 py-3">
                            NOMBRE
                        </th>
                        <th scope="col" class="px-10 py-3">
                            FAMILIA
                        </th>
                        <th scope="col" class="px-6 py-3">
                            CATEGORÍA
                        </th>
                        <th scope="col" class="px-6 py-3">
                            UNIDAD
                        </th>
                        <th scope="col" class="px-6 py-3">
                            PROVEEDOR
                        </th>
                        <th scope="col" class="px-4 py-3">
                            ALMACÉN
                        </th>
                        <th scope="col" class="px-4 py-3">
                            BAR
                        </th>
                        <th scope="col" class="px-4 py-3">
                            BARRA
                        </th>
                        <th scope="col" class="px-4 py-3">
                            CADDIE
                        </th>
                        <th scope="col" class="px-4 py-3">
                            CAFETERÍA
                        </th>
                        <th scope="col" class="px-4 py-3">
                            COCINA
                        </th>
                        <th scope="col" class="px-6 py-3">
                            COSTO UNITARIO
                        </th>
                        <th scope="col" class="px-6 py-3">
                            ESTADO
                        </th>
                        <th scope="col" class="px-6 py-3">
                            ACCIONES
                        </th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        {{-- <div class="w-full my-2 flex justify-end">{{ $listaArticulos->links() }}</div> --}}
    </div>

    {{-- Modal para añadir al catalogo --}}
    
    <x-modal name="añadir" title="AÑADIR AL CATÁLOGO">
        <x-slot:body>
            <form class="p-1 md:p-1">
                <div class="grid gap-1 mb-1 grid-cols-4">
                    {{-- CODIGO --}}
                    <div class="col-span-1">
                        <label for="codigo" class="text-sm font-medium text-gray-900 dark:text-white">Código</label>
                        <input type="text" id="codigo" aria-label="codigo"
                            class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            value="Código" disabled>
                    </div>
                    {{-- TIPO --}}
                    <div class="col-span-1">
                        <label for="tipo" class="text-sm font-medium text-gray-900 dark:text-white">Tipo</label>
                        <select id="tipo"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option selected="">SELECCIONAR</option>
                            <option value="TV">TV/Monitors</option>
                            <option value="PC">PC</option>
                            <option value="GA">Gaming/Console</option>
                            <option value="PH">Phones</option>
                        </select>
                    </div>
                    {{-- NOMBRE --}}
                    <div class="col-span-2">
                        <label for="nombre" class="text-sm font-medium text-gray-900 dark:text-white">Nombre</label>
                        <input type="text" name="nombre" id="nombre"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Nombre" required="">
                    </div>
                    {{-- FAMILIA --}}
                    <div class="col-span-1">
                        <label for="familia" class="text-sm font-medium text-gray-900 dark:text-white">Familia</label>
                        <select id="familia"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option selected="">SELECCIONAR</option>
                            <option value="TV">TV/Monitors</option>
                            <option value="PC">PC</option>
                            <option value="GA">Gaming/Console</option>
                            <option value="PH">Phones</option>
                        </select>
                    </div>
                    {{-- CATEGORIA --}}
                    <div class="col-span-1">
                        <label for="categoria"
                            class="text-sm font-medium text-gray-900 dark:text-white">Categoría</label>
                        <select id="categoria"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option selected="">SELECCIONAR</option>
                            <option value="TV">TV/Monitors</option>
                            <option value="PC">PC</option>
                            <option value="GA">Gaming/Console</option>
                            <option value="PH">Phones</option>
                        </select>
                    </div>
                    {{-- UNIDAD --}}
                    <div class="col-span-1">
                        <label for="unidad" class="text-sm font-medium text-gray-900 dark:text-white">Unidad</label>
                        <select id="unidad"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option selected="">SELECCIONAR</option>
                            <option value="TV">TV/Monitors</option>
                            <option value="PC">PC</option>
                            <option value="GA">Gaming/Console</option>
                            <option value="PH">Phones</option>
                        </select>
                    </div>
                    {{-- PROVEEDOR --}}
                    <div class="col-span-1">
                        <label for="proveedor"
                            class="text-sm font-medium text-gray-900 dark:text-white">Proveedor</label>
                        <select id="proveedor"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option selected="">SELECCIONAR</option>
                            <option value="TV">TV/Monitors</option>
                            <option value="PC">PC</option>
                            <option value="GA">Gaming/Console</option>
                            <option value="PH">Phones</option>
                        </select>
                    </div>
                    {{-- TABLA PARA DETERMINAR STOCKS DE CADA PUNTO --}}
                    <div class="col-span-4">
                        <label for="proveedor"
                            class="text-sm font-medium text-gray-900 dark:text-white">Stocks</label>
                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-center">
                                            STOCK
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-center">
                                            ALMACÉN
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-center">
                                            BAR
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-center">
                                            BARRA
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-center">
                                            CADDIE
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-center">
                                            CAFETERÍA
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                        <th
                                            class="px-6 py-4 font-medium text-center text-gray-900 whitespace-nowrap dark:text-white">
                                            MÍNIMO
                                        </th>
                                        <td class="text-center px-6 py-4">
                                            <input type="number" id="st_min"
                                                class="w-20 p-1.5 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </td>
                                        <td class="text-center px-6 py-4">
                                            <input type="number" id="st_min"
                                                class="w-20 p-1.5 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </td>
                                        <td class="text-center px-6 py-4">
                                            <input type="number" id="st_min"
                                                class="w-20 p-1.5 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </td>
                                        <td class="text-center px-6 py-4">
                                            <input type="number" id="st_min"
                                                class="w-20 p-1.5 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </td>
                                        <td class="text-center px-6 py-4">
                                            <input type="number" id="st_min"
                                                class="w-20 p-1.5 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </td>
                                    </tr>
                                    <tr
                                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                        <th
                                            class="px-6 py-4 font-medium text-center text-gray-900 whitespace-nowrap dark:text-white">
                                            MÁXIMO
                                        </th>
                                        <td class="text-center px-6 py-4">
                                            <input type="number" id="st_max"
                                                class="w-20 p-1.5 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </td>
                                        <td class="text-center px-6 py-4">
                                            <input type="number" id="st_max"
                                                class="w-20 p-1.5 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </td>
                                        <td class="text-center px-6 py-4">
                                            <input type="number" id="st_max"
                                                class="w-20 p-1.5 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </td>
                                        <td class="text-center px-6 py-4">
                                            <input type="number" id="st_max"
                                                class="w-20 p-1.5 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </td>
                                        <td class="text-center px-6 py-4">
                                            <input type="number" id="st_max"
                                                class="w-20 p-1.5 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{-- COSTO UNITARIO --}}
                    <div class="col-span-1">
                        <label for="costo" class="text-sm font-medium text-gray-900 dark:text-white">Costo Unitario</label>
                        <input type="number" id="costo"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Costo" required />
                    </div>
                </div>
            </form>
        </x-slot>
        <x-slot:footer>
            <button type="button" wire:click="register"
                class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Aceptar
            </button>
            <button x-on:click="show = false" type="button"
                class="py-2.5 px-5 ms-3 text-sm font-medium text-red-900 focus:outline-none bg-red rounded-lg border border-red-200 hover:bg-red-100 hover:text-red-700 focus:z-10 focus:ring-4 focus:ring-red-100 dark:focus:ring-red-700 dark:bg-red-800 dark:text-red-400 dark:border-red-600 dark:hover:text-white dark:hover:bg-red-700">Cancelar
            </button>
        </x-slot>
    </x-modal>
    
</div>
