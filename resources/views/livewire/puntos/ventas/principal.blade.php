<div>
    <!--Inputs-->
    <form class="flex gap-2" wire:submit="refresh">
        {{-- Barra de busqueda --}}
        <div class="w-96 ms-3 mx-3">
            <label for="default-search"
                class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
            <div class="relative">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                    </svg>
                </div>
                <input wire:model="search" type="text" id="default-search"
                    class="w-full p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Nombre o numero de socio" />
            </div>
        </div>
        {{-- fecha --}}
        <div class="flex ">
            <div class="w-48">
                <input type="date" id="fecha" wire:model="fecha"
                    x-show= "$wire.tipo_vista == 'todo' ? true : false"
                    class="mx-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            </div>
        </div>
        {{-- Select Pendientes --}}
        <div class="flex grow ">
            <select id="tipo_venta" wire:model="tipo_vista" title="Vista de tabla"
                class="max-w-32 mx-5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="todo" selected>Todos</option>
                <option value="abierta">Abiertas</option>
                <option value="pendiente">Pendientes</option>
            </select>
        </div>

        <!--Boton de busqueda -->
        <button type="submit"
            class="w-32 mx-3 justify-center text-center inline-flex items-center text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">
            <div wire:loading.delay wire:target='refresh' class="me-4">
                @include('livewire.utils.loading', ['w' => 5, 'h' => 5])
            </div>
            Buscar
        </button>
    </form>
    {{-- TABLA DE VENTAS Y PENDIENTES --}}
    @if ($tipo_vista != 'pendiente')
        @include('livewire.puntos.ventas.include.tabla-ventas')
    @else
        @include('livewire.puntos.ventas.include.tabla-pendientes')
    @endif
    {{-- Linea divisora, regresar , pasar venta --}}
    <div class="ms-3 mx-3">
        <hr class="h-px my-2 bg-gray-300 border-0 dark:bg-gray-700">
        @if ($codigopv != 'REC')
            <button type="button" x-data x-on:click="$dispatch('open-modal', {name:'modalAdvertencia'})"
                class="my-2 text-green-700 hover:text-white border border-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-500 dark:focus:ring-green-800">
                Pasar ventas
            </button>
        @endif
    </div>

    {{-- MODAL DE ADVERTENCIA DE TRASPASO DE VENTAS --}}
    <x-modal title="Pasar ventas" name="modalAdvertencia">
        <x-slot name='body'>
            <div wire:loading.remove wire:target='pasarVentas'
            class="text-center">
                <div class="text-center">
                    <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <h3 class="mb-5 text-xl font-normal text-gray-500 dark:text-gray-400">¡¡ Advertencia !!
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400 w-96">
                        ¿Realmente deseas traspasar las ventas abiertas al siguiente turno?
                    </p>
                    <p id="status" class="my-4 font-bold">
                        {{ $status_message }}
                    </p>
                </div>
                <button type="button" wire:click ="pasarVentas"
                    class="mt-3 focus:outline-none text-white bg-green-500 hover:bg-green-600 focus:ring-4 focus:ring-green-400 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:focus:ring-green-900">
                    Confirmar trapaso y cerrar caja
                </button>
            </div>

            <div wire:loading wire:target='pasarVentas'>
                <div
                    class="flex items-center justify-center bg-neutral-secondary-soft h-56 w-96 border border-default text-fg-brand-strong text-xs font-medium rounded-base">
                    <p
                        class="text-sm p-4 ring-1 ring-inset ring-brand-subtle text-fg-brand-strong font-medium rounded-sm bg-brand-softer animate-pulse">
                        Generando movimientos... 
                    </p>
                </div>
            </div>
        </x-slot>
    </x-modal>
</div>
