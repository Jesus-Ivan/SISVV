<div class="ms-3 mx-3">
    {{-- FILTRO DE BUSQUEDA --}}
    <form class="relative w-96" wire:submit='search' method="GET">
        @csrf
        <div class="flex items-end gap-4 grow">
            <label for="default-search"
                class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
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

    {{-- TABLA DE STOCKS --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg my-2">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-3 py-3 max-w-1.5">
                        CÓDIGO
                    </th>
                    <th scope="col" class="px-6 py-3 text-center ">
                        NOMBRE
                    </th>
                    <th scope="col" class="px-3 py-3 text-center">
                        ALMACÉN
                    </th>
                    <th scope="col" class="px-3 py-3 text-center">
                        BAR
                    </th>
                    <th scope="col" class="px-3 py-3 text-center">
                        CADDIE
                    </th>
                    <th scope="col" class="px-3 py-3 text-center">
                        CAFETERÍA
                    </th>
                    <th scope="col" class="px-3 py-3 text-center">
                        COCINA
                    </th>
                    <th scope="col" class="px-3 py-3 text-center">
                        L. DAMAS
                    </th>
                    <th scope="col" class="px-3 py-3 text-center">
                        L. CABALLEROS
                    </th>
                    <th scope="col" class="px-3 py-3 text-center">
                        RESTAURANT
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->articulos as $index => $articulo)
                    <tr wire:key='{{ $articulo->id }}'
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                        <th scope="row"
                            class="px-6 py-1 font-medium text-center max-w-1.5 text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $articulo->codigo }}
                        </th>
                        <td class="px-6 py-1 min-w-[200px] max-w-[400px]">
                            {{ $articulo->nombre }}
                        </td>
                        <td class="px-3 py-1 text-center min-w-[120px] uppercase">
                            @foreach ($articulo->stocks as $stock)
                                <p>
                                    {{ substr($stock->tipo, 0, 1) }} :
                                    @if ($stock->stock_alm <= 0)
                                        <span
                                            class="bg-red-100 text-red-800 text-xs font-medium  px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                            {{ $stock->stock_alm }}
                                        </span>
                                    @else
                                        <span
                                            class="bg-green-100 text-green-800 text-xs font-medium  px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                            {{ $stock->stock_alm }}
                                        </span>
                                    @endif
                                </p>
                            @endforeach
                        </td>
                        <td class="px-3 py-1 text-center min-w-[120px] uppercase">
                            @foreach ($articulo->stocks as $stock)
                                <p>
                                    {{ substr($stock->tipo, 0, 1) }} :
                                    @if ($stock->stock_bar <= 0)
                                        <span
                                            class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                            {{ $stock->stock_bar }}
                                        </span>
                                    @else
                                        <span
                                            class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                            {{ $stock->stock_bar }}
                                        </span>
                                    @endif
                                </p>
                            @endforeach
                        </td>
                        <td class="px-3 py-1 text-center min-w-[120px] uppercase">
                            @foreach ($articulo->stocks as $stock)
                                <p>
                                    {{ substr($stock->tipo, 0, 1) }} :
                                    @if ($stock->stock_cad <= 0)
                                        <span
                                            class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                            {{ $stock->stock_cad }}
                                        </span>
                                    @else
                                        <span
                                            class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                            {{ $stock->stock_cad }}
                                        </span>
                                    @endif
                                </p>
                            @endforeach
                        </td>
                        <td class="px-3 py-1 text-center min-w-[120px] uppercase">
                            @foreach ($articulo->stocks as $stock)
                                <p>
                                    {{ substr($stock->tipo, 0, 1) }} :
                                    @if ($stock->stock_caf <= 0)
                                        <span
                                            class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                            {{ $stock->stock_caf }}
                                        </span>
                                    @else
                                        <span
                                            class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                            {{ $stock->stock_caf }}
                                        </span>
                                    @endif
                                </p>
                            @endforeach
                        </td>
                        <td class="px-3 py-1 text-center min-w-[120px] uppercase">
                            @foreach ($articulo->stocks as $stock)
                                <p>
                                    {{ substr($stock->tipo, 0, 1) }} :
                                    @if ($stock->stock_coc <= 0)
                                        <span
                                            class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                            {{ $stock->stock_coc }}
                                        </span>
                                    @else
                                        <span
                                            class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                            {{ $stock->stock_coc }}
                                        </span>
                                    @endif
                                </p>
                            @endforeach
                        </td>
                        <td class="px-3 py-1 text-center min-w-[120px] uppercase">
                            @foreach ($articulo->stocks as $stock)
                                <p>
                                    {{ substr($stock->tipo, 0, 1) }} :
                                    @if ($stock->stock_lod <= 0)
                                        <span
                                            class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                            {{ $stock->stock_lod }}
                                        </span>
                                    @else
                                        <span
                                            class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                            {{ $stock->stock_lod }}
                                        </span>
                                    @endif
                                </p>
                            @endforeach
                        </td>
                        <td class="px-3 py-1 text-center min-w-[120px] uppercase">
                            @foreach ($articulo->stocks as $stock)
                                <p>
                                    {{ substr($stock->tipo, 0, 1) }} :
                                    @if ($stock->stock_loc <= 0)
                                        <span
                                            class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                            {{ $stock->stock_loc }}
                                        </span>
                                    @else
                                        <span
                                            class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                            {{ $stock->stock_loc }}
                                        </span>
                                    @endif
                                </p>
                            @endforeach
                        </td>
                        <td class="px-3 py-1 text-center min-w-[120px] uppercase">
                            @foreach ($articulo->stocks as $stock)
                                <p>
                                    {{ substr($stock->tipo, 0, 1) }} :
                                    @if ($stock->stock_res <= 0)
                                        <span
                                            class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                            {{ $stock->stock_res }}
                                        </span>
                                    @else
                                        <span
                                            class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                            {{ $stock->stock_res }}
                                        </span>
                                    @endif
                                </p>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="w-full my-2 flex justify-end">{{ $this->articulos->links() }}</div>
</div>
