<div class="ms-3 mx-3" x-data @keyup.ctrl.window="$dispatch('open-modal', { name: 'añadirMr' })">
    {{-- SEARCH BAR --}}
    <div class="flex gap-4 items-end">
        <div>
            <label for="clave_dpto" class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Origen de
                Merma</label>
            <select id="clave_dpto" wire:model='merma_seleccionada'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                <option selected value="{{ null }}">SELECCIONAR</option>
                @foreach ($this->bodegas as $index => $bodega)
                    <option value="{{ $bodega->clave }}">{{ $bodega->descripcion }}</option>
                @endforeach
            </select>
        </div>
        <div >
            <label for="st_min" class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Mes</label>
            <input type="date" name="st_min" wire:model='mes_seleccionado'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                placeholder="Unitario">
        </div>
        <div>
            <button type="button" wire:click='searchMerma'
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                <svg wire:loading.remove wire:target='searchMerma' class="w-6 h-6" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                        d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                </svg>
                <!--Loading indicator-->
                <div wire:loading wire:target='searchMerma'>
                    @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
                </div>
                <span class="sr-only">Buscar</span>
            </button>
        </div>
    </div>
    {{-- TABLA --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="w-14 px-4 py-3">
                        FOLIO
                    </th>
                    <th scope="col" class="px-6 py-3">
                        ARTICULO
                    </th>
                    <th scope="col" class="px-6 py-3">
                        OBSERVACIONES
                    </th>
                    <th scope="col" class="px-6 py-3">
                        CANTIDAD
                    </th>
                    <th scope="col" class="px-6 py-3">
                        UNIDAD
                    </th>
                    <th scope="col" class="px-6 py-3">
                        ORIGEN
                    </th>
                    <th scope="col" class="px-6 py-3">
                        TIPO
                    </th>
                    <th scope="col" class="px-6 py-3">
                        FECHA
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->mermas as $index => $item)
                    <tr wire:key='{{ $index }}'
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                        <th scope="row"
                            class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $item->folio }}
                        </th>
                        <td class="px-6 py-2">
                            {{ $item->nombre }}
                        </td>
                        <td class="px-6 py-2">
                            {{ $item->observaciones }}
                        </td>
                        <td class="px-6 py-2">
                            {{ $item->cantidad }}
                        </td>
                        <td class="px-6 py-2">
                            {{ $item->unidad->descripcion }}
                        </td>
                        <td class="px-6 py-2">
                            {{ $item->bodega->descripcion }}
                        </td>
                        <td class="px-6 py-2">
                            {{ $item->tipo->nombre }}
                        </td>
                        <td class="px-6 py-2">
                            {{ $item->created_at }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div>
        {{ $this->mermas->links() }}
    </div>
    {{-- Action message --}}
    <x-action-message on='open-action-message'>
        @if (session('success'))
            <div id="alert-exito"
                class="flex items-center p-4 mb-4 text-green-800 border-t-4 border-green-300 bg-green-50 dark:text-green-400 dark:bg-gray-800 dark:border-green-800"
                role="alert">
                <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                </svg>
                <div class="ms-3 text-sm font-medium">
                    {{ session('success') }}
                </div>
            </div>
        @else
            <div id="alert-error"
                class="flex items-center p-4 mb-4 text-red-800 border-t-4 border-red-300 bg-red-50 dark:text-red-400 dark:bg-gray-800 dark:border-red-800"
                role="alert">
                <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                </svg>
                <div class="ms-3 text-sm font-medium">
                    {{ session('fail') }}
                </div>
            </div>
        @endif
    </x-action-message>

    @include('livewire.almacen.mermas.modal-mermas')
</div>
