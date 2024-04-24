<div>
    <div class="inline-flex flex-grow">
        {{-- SELECCIONAR EL DESTINO DE LA SALIDA --}}
        <form class="ms-3 max-w-sm">
            <label for="destino" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Destino:</label>
            <select id="destino"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option selected>SELECCIONAR</option>
                <option value="OFICINA">CAMPO</option>
                <option value="YO">MANTENIMIENTO</option>
                <option value="NO SE XD">SOCIO</option>
            </select>
            {{-- @error('confirmar')
            <x-input-error messages="{{ $message }}" />
        @enderror --}}
        </form>

        {{-- CONTIENE LAS POSIBLES OBSERVACIONES PARA AUTORIZAR UNA SALIDA --}}
        <form class="ms-3 max-w-sm">
            <label for="observaciones"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nota:</label>
            <select id="observaciones"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option selected>SELECCIONAR</option>
                <option value="OFICINA">AUTORIZADO POR OFICINAS</option>
                <option value="YO">AUTORIZADO POR MI</option>
                <option value="NO SE XD">YA NO SE ME OCURRIO OTRA COSA</option>
            </select>
            {{-- @error('confirmar')
            <x-input-error messages="{{ $message }}" />
        @enderror --}}
        </form>
    </div>

    {{-- Tabla --}}
    <div class="ms-3 mx-3 my-3">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            CÓDIGO
                        </th>
                        <th scope="col" class="px-6 py-3 w-96">
                            DESCRIPCIÓN
                        </th>
                        <th scope="col" class="px-6 py-3">
                            CANTIDAD
                        </th>
                        <th scope="col" class="px-4 py-3">
                            EXISTENCIAS ALMACEN
                        </th>
                        <th scope="col" class="px-6 py-3">
                            ACCIONES
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-4">
                            1234
                        </td>
                        <td class="px-6 py-4">
                            AGUA CIEL 600 ML
                        </td>
                        <td class="px-6 py-4">
                            5
                        </td>
                        <td class="px-6 py-4">
                            100
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex">
                                <button type="button"
                                    class="text-gray-700 hover:text-white border border-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-3.5 py-1.5 text-center me-2 mb-2 dark:border-gray-500 dark:text-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                        class="w-5 h-5">
                                        <path
                                            d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z" />
                                        <path
                                            d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z" />
                                    </svg>
                                </button>
                                <button type="button"
                                    class="text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-3.5 py-1.5 text-center me-2 mb-2 dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                        class="w-5 h-5">
                                        <path fill-rule="evenodd"
                                            d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal para dar salida a articulos --}}
    <x-modal name="nuevaSalida" title="SALIDA DE ARTICULO">
        <x-slot:body>
            <form class="p-1 md:p-1">
                <div class="grid gap-1 mb-1 grid-cols-3">
                    {{-- BARRA DE BUSQUEDA --}}
                    <div class="col-span-3">
                        <label for="name" class="mb-2 text-sm font-medium text-gray-900 dark:text-white">Buscar
                            articulo</label>
                        <div class="relative">
                            <label for="default-search"
                                class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input wire:model.live.debounce.500ms="search" type="search" id="search"
                                class="block w-full p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Buscar folio" required />
                        </div>
                    </div>
                    {{-- CODIGO DE ARTICULO --}}
                    <div class="col-span-1">
                        <label for="codigo"
                            class="mb-2 text-sm font-medium text-gray-900 dark:text-white">Código</label>
                        <input type="text" id="disabled-input" aria-label="disabled input"
                            class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            value="Código" disabled>
                    </div>
                    {{-- NOMBRE DE ARTICULO --}}
                    <div class="col-span-2">
                        <label for="nombre"
                            class="mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre</label>
                        <input type="text" id="disabled-input" aria-label="disabled input"
                            class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            value="Nombre" disabled>
                    </div>
                    {{-- ORIGEN DE SALIDA --}}
                    <div class="col-span-1">
                        <label for="salida" class="mb-2 text-sm font-medium text-gray-900 dark:text-white">Origen
                            Salida</label>
                        <input type="text" id="disabled-input" aria-label="disabled input"
                            class=" bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            value="Salida" disabled>
                    </div>
                    {{-- EXISTENCIAS EN EL ORIGEN --}}
                    <div class="col-span-1">
                        <label for="stock"
                            class="mb-2 text-sm font-medium text-gray-900 dark:text-white">Existencias</label>
                        <input type="text" id="disabled-input" aria-label="disabled input"
                            class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            value="1" disabled>
                    </div>
                    {{-- CANTIDAD PARA DAR SALIDA --}}
                    <div class="col-span-1 sm:col-span-1">
                        <label for="cantidad"
                            class="mb-2 text-sm font-medium text-gray-900 dark:text-white">Cantidad</label>
                        <input type="number" name="st_min" id="st_min"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Cantidad" required="">
                    </div>
                </div>
            </form>
        </x-slot>
        <x-slot:footer>
            <button type="button"
                class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Agregar
            </button>
            <button x-on:click="show = false" type="button"
                class="py-2.5 px-5 ms-3 text-sm font-medium text-red-900 focus:outline-none bg-red rounded-lg border border-red-200 hover:bg-red-100 hover:text-red-700 focus:z-10 focus:ring-4 focus:ring-red-100 dark:focus:ring-red-700 dark:bg-red-800 dark:text-red-400 dark:border-red-600 dark:hover:text-white dark:hover:bg-red-700">Cancelar
            </button>
        </x-slot>
    </x-modal>

</div>
