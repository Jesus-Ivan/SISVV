<div>
    {{-- Contenido --}}
    <div class="container py-3">
        <div class="flex ms-3">
            <div class="inline-flex flex-grow">
                <h4 class="flex items-center ms-2 text-2xl font-bold dark:text-white">HISTORIAL DE PRODUCTOS</h4>
            </div>
        </div>
    </div>
    {{-- busqueda --}}
    <form class="flex gap-5 items-center pb-4" wire:submit='buscar' method="GET">
        @csrf
        {{-- INPUT SEARCH --}}
        <div class="relative">
            <div class="absolute inset-y-0 left-0 rtl:inset-r-0 rtl:right-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="currentColor"
                    viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                        clip-rule="evenodd"></path>
                </svg>
            </div>
            <input type="text" wire:model ='input_search'
                class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Codigo o nombre del producto">
        </div>
        {{-- BOTON DE BUSQUEDA --}}
        <button type="button" wire:click='buscar'
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
    {{-- Tabla --}}
    <div class="ms-3 mx-3 overflow-x-auto">
        <table class="table  w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        FECHA CONSULTA
                    </th>
                    <th scope="col" class="px-6 py-3">
                        FOLIO DE COMPRA
                    </th>
                    <th scope="col" class="px-6 py-3">
                        CÓDIGO
                    </th>
                    <th scope="col" class="px-4 py-3">
                        DESCRIPCIÓN
                    </th>
                    <th scope="col" class="px-4 py-3">
                        CANTIDAD
                    </th>
                    <th scope="col" class="px-4 py-3">
                        COSTO UNITARIO
                    </th>
                    <th scope="col" class="px-4 py-3">
                        TOTAL
                    </th>
                    <th scope="col" class="px-6 py-3">
                        IVA
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->productos as $index => $producto)
                    <tr wire:key='{{ $index }}'
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-4">
                            {{ $producto->consultado }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $producto->folio_orden }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $producto->codigo_producto }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $producto->nombre }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $producto->cantidad }}
                        </td>
                        <td class="px-6 py-4">
                            ${{ $producto->costo_unitario }}
                        </td>
                        <td class="px-6 py-4">
                            ${{ $producto->importe }}
                        </td>
                        <td class="px-6 py-4">
                            ${{ $producto->iva }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{-- PAGINADOR --}}
    <div>
        {{ $this->productos->links() }}
    </div>
</div>
