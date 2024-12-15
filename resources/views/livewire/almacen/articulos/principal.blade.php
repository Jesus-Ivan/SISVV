<div class="ms-3 me-3">
    {{-- FILTRO DE BUSQUEDA --}}
    <form class="relative w-96" wire:submit='search' method="GET">
        @csrf
        <div class="flex items-end gap-4 grow">
            <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                </svg>
            </div>
            <input wire:model="search_input" type="text"
                class="block w-full p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Buscar nombre de artículo o código" />
            <!--Loading indicator-->
            <div wire:loading>
                @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
            </div>
        </div>
    </form>

    {{-- RADIO BUTONS AND SELECT --}}
    <div class="flex gap-3 items-center my-2">
        <!--SELECT -->
        <div>
            <select id="vista" wire:model.live.debounce.600ms ='vista'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option selected value="COM">Completa</option>
                <option value="ACT">Activo</option>
                <option value="INA">Inactivo</option>
            </select>
        </div>
        <!--RADIO BUTTONS-->
        <div>
            <label for="radio-buttons"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Departamento</label>
            <div class="flex gap-8 m-2" id="radio-buttons">
                <div class="flex items-center">
                    <input id="T-radio" type="radio" value="T" name="todos"
                        wire:model.live.debounce.500ms='radioButon'
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <label for="T-radio" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Mostrar
                        todos</label>
                </div>
                <div class="flex items-center-2">
                    <input id="A-radio" type="radio" value="A" name="almacen"
                        wire:model.live.debounce.500ms='radioButon'
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <label for="P-radio"
                        class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Almacén</label>
                </div>
                <div class="flex items-center">
                    <input id="PV-radio" type="radio" value="PV" name="puntos"
                        wire:model.live.debounce.500ms='radioButon'
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <label for="C-radio" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Puntos
                        Venta</label>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLA --}}
    <div class="my-2">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            CÓDIGO
                        </th>
                        <th scope="col" class="px-10 py-3">
                            FAMILIA
                        </th>
                        <th scope="col" class="px-6 py-3">
                            CATEGORÍA
                        </th>
                        <th scope="col" class="px-20 py-3">
                            NOMBRE
                        </th>
                        <th scope="col" class="px-6 py-3">
                            PROVEEDOR
                        </th>
                        <th scope="col" class="px-6 py-3">
                            PRECIO VENTA
                        </th>
                        <th scope="col" class="px-6 py-3">
                            ULTIMA COMPRA
                        </th>
                        <th scope="col" class="px-6 py-3">
                            DEPTO
                        </th>
                        <th scope="col" class="px-6 py-3">
                            TIPO
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
                    @foreach ($this->articulos as $index => $articulo)
                        <tr wire:key='{{ $articulo->codigo }}'
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-1">
                                {{ $articulo->codigo }}
                            </td>
                            <td class="px-6 py-1 uppercase">
                                {{ $articulo->familia->nombre }}
                            </td>
                            <td class="px-6 py-1 uppercase">
                                {{ $articulo->categoria->nombre }}
                            </td>
                            <td class="px-6 py-1 uppercase">
                                {{ $articulo->nombre }}
                            </td>
                            <td class="px-6 py-1 uppercase">
                                {{ $articulo->proveedor->nombre }}
                            </td>
                            <td class="px-6 py-1">
                                ${{ $articulo->costo_unitario }}
                            </td>
                            <td class="px-6 py-1">
                                {{ $articulo->ultima_compra }}
                            </td>
                            <td class="px-6 py-1">
                                {{ $articulo->clave_depto }}
                            </td>
                            <td class="px-6 py-1">
                                {{ $articulo->tipo }}
                            </td>
                            <td class="px-6 py-1">
                                @if ($articulo->estado == '1')
                                    <span
                                        class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">ACTIVO
                                    </span>
                                @else
                                    <span
                                        class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">INACTIVO
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-1">
                                <div class="text-center">
                                    <a href="{{ route('almacen.articulos.editar', $articulo->codigo) }}">
                                        <button type="button"
                                            class="text-green-700 hover:text-white border border-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-3.5 py-1.5 text-center me-2 mb-2 dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-600 dark:focus:ring-green-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                fill="currentColor" class="w-4 h-4">
                                                <path
                                                    d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z" />
                                                <path
                                                    d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z" />
                                            </svg>
                                        </button>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="w-full my-2 flex justify-end">{{ $this->articulos->links() }}</div>
    </div>
</div>
