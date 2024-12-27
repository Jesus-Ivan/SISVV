<div>
    {{-- SEARCH BAR --}}
    <form class="flex items-end gap-2 m-3" method="GET" wire:submit='buscar'>
        @csrf
        {{-- Input search --}}
        <div class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between ">
            <label for="table-search" class="sr-only">Buscar</label>
            <div class="relative">
                <input type="text" wire:model='search_input'
                    class="block p-2.5  text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Codigo, descripción ...">
            </div>
        </div>
        <!--CHECKBOX-->
        <div class="w-80">
            <ul
                class="items-center w-full text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg sm:flex dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r dark:border-gray-600">
                    <div class="flex items-center ps-3">
                        <input id="item-punto" type="checkbox" wire:model='departamento.PV'
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                        <label for="item-punto"
                            class="w-full py-2.5 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Punto
                            venta</label>
                    </div>
                </li>
                <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r dark:border-gray-600">
                    <div class="flex items-center ps-3">
                        <input id="item-almacen" type="checkbox" wire:model='departamento.ALM'
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                        <label for="item-almacen"
                            class="w-full py-2.5 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Almacen</label>
                    </div>
                </li>
            </ul>
        </div>
        {{-- BOTON DE BUSQUEDA --}}
        <button type="submit" wire:click='buscar'
            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            <svg wire:loading.remove wire:target='buscar' class="w-6 h-6" aria-hidden="true"
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
    </form>
    <!--Tabla de articulos-->
    <div class="mx-3">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-sm">
                            CÓDIGO
                        </th>
                        <th scope="col" class="px-6 py-3 text-sm">
                            FAMILIA
                        </th>
                        <th scope="col" class="px-6 py-3 text-sm">
                            CATEGORÍA
                        </th>
                        <th scope="col" class="px-6 py-3 text-sm">
                            DESCRIPCIÓN
                        </th>
                        <th scope="col" class="px-4 py-3 text-sm">
                            COSTO UNITARIO
                        </th>
                        <th scope="col" class="px-4 py-3 text-sm">
                            EXISTENCIAS
                        </th>
                        <th scope="col" class="px-4 py-3 text-sm">
                            EXISTENCIAS ALMACÉN
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($this->productos as $key => $producto)
                        <tr wire:key='{{ $key }}'
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-500">
                            <td class="px-6 py-2 font-bold">
                                {{ $producto->codigo }}
                            </td>
                            <td class="px-6 py-2 font-bold">
                                {{ $producto->familia->nombre }}
                            </td>
                            <td class="px-6 py-2 font-bold">
                                {{ $producto->categoria->nombre }}
                            </td>
                            <td class="px-6 py-2 font-bold">
                                {{ $producto->nombre }}
                            </td>
                            <td class="px-4 py-2 font-bold">
                                $ {{ $producto->costo_unitario }}
                            </td>
                            <td class="px-4 py-2 font-bold">
                                @foreach ($producto->stocks as $stock)
                                    <p class="uppercase">{{ $stock->tipo }} : {{ $stock[$clave_stock[$codigopv]] }}</p>
                                @endforeach
                            </td>
                            <td class="px-4 py-2 font-bold">
                                @foreach ($producto->stocks as $stock)
                                    <p class="uppercase">{{ $stock->tipo }} : {{ $stock->stock_alm }}</p>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div>
                {{ $this->productos->links() }}
            </div>
        </div>
    </div>
</div>
