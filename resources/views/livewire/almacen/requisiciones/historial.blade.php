<div>
    {{-- Contenido --}}
    <div class="container py-3">
        <div class="flex mx-3">
            <div class="inline-flex flex-grow">
                <h4 class="flex items-center text-2xl font-bold dark:text-white">HISTORIAL DE REQUISICIONES</h4>
            </div>
        </div>
    </div>
    {{-- Search bar --}}
    <div class="flex gap-4 items-end mx-3">
        {{-- Fecha inicio --}}
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Fecha de inicio</label>
            <input datepicker type="date" wire:model='fInicio'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-36 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        </div>
        {{-- Fecha fin --}}
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Fecha de fin</label>
            <input type="date" wire:model='fFin'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-36 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        </div>
        {{-- Campo de entrada --}}
        <div class="flex-1">
            <livewire:search-bar tittle="Codigo o nombre de presentacion" table="presentaciones" :columns="['clave', 'descripcion']"
                primary="clave" event="selected-articulo" />
        </div>

        {{-- BOTON DE BUSQUEDA --}}
        <button type="button" wire:click='buscar'
            class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
            <div wire:loading.remove wire:target='buscar'>
                Todos
            </div>
            <!--Loading indicator-->
            <div wire:loading wire:target='buscar'>
                @include('livewire.utils.loading', ['w' => 5, 'h' => 5])
            </div>
            <span class="sr-only">Buscar</span>
        </button>
    </div>

    {{-- Tabla --}}
    <div class="ms-3 mx-3 overflow-x-auto">
        <table class="table  w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-3 py-3">
                        FECHA REQUISICION
                    </th>
                    <th scope="col" class="px-3 py-3">
                        FOLIO REQUISICION
                    </th>
                    <th scope="col" class="px-3 py-3">
                        #
                    </th>
                    <th scope="col" class="px-3 py-3">
                        PRESENTACION
                    </th>
                    <th scope="col" class="px-3 py-3">
                        CANTIDAD
                    </th>
                    <th scope="col" class="px-3 py-3">
                        COSTO UNITARIO
                    </th>
                    <th scope="col" class="px-3 py-3">
                        TOTAL
                    </th>
                    <th scope="col" class="px-3 py-3">
                        IVA
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->productos as $index => $producto)
                    <tr wire:key='{{ $index }}'
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-3 py-2">
                            {{ $producto->created_at }}
                        </td>
                        <td class="px-3 py-2">
                            {{ $producto->folio_requisicion }}
                        </td>
                        <td class="px-3 py-2">
                            {{ $producto->clave_presentacion }}
                        </td>
                        <td class="px-3 py-2">
                            {{ $producto->descripcion }}
                        </td>
                        <td class="px-3 py-2">
                            {{ $producto->cantidad }}
                        </td>
                        <td class="px-3 py-2">
                            ${{ $producto->costo_unitario }}
                        </td>
                        <td class="px-3 py-2">
                            ${{ $producto->importe }}
                        </td>
                        <td class="px-3 py-2">
                            {{ $producto->iva }} %
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
    {{-- Action message --}}
    <x-action-message on='busqueda'>
        @if (session('fail-busqueda'))
            <div id="alert-error"
                class="flex items-center p-4 mb-4 text-red-800 border-t-4 border-red-300 bg-red-50 dark:text-red-400 dark:bg-gray-800 dark:border-red-800"
                role="alert">
                <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                </svg>
                <div class="ms-3 text-sm font-medium">
                    {{ session('fail-busqueda') }}
                </div>
            </div>
        @endif
    </x-action-message>
</div>
