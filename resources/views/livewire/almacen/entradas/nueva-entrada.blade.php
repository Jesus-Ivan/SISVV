<div class="p-2">
    {{-- BARRA DE BUSQUEDA --}}
    <form wire:submit='buscarOrden' method="GET">
        @csrf
        <div class="flex mb-2 w-96">
            <div class="relative w-full">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3">
                    <svg wire:loading.remove wire:target='buscarOrden' class="w-4 h-4 text-gray-500 dark:text-gray-400"
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                    </svg>
                    <!--Loading indicator-->
                    <div wire:loading wire:target='buscarOrden'>
                        @include('livewire.utils.loading', ['w' => 5, 'h' => 5])
                    </div>
                </div>
                <input wire:model="folio_search" type="text"
                    class="block w-full p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Folio orden de compra" required />
            </div>
            <button type="submit" class="w-0 h-0" />
        </div>
    </form>
    {{-- DETALLES ORDEN COMPRA (TABLA) --}}
    <div class="overflow-y-auto h-96">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-3 py-2">
                        CÓDIGO
                    </th>
                    <th scope="col" class=" px-6 py-2">
                        DESCRIPCIÓN
                    </th>
                    <th scope="col" class=" px-6 py-2">
                        PROVEEDOR
                    </th>
                    <th scope="col" class="px-6 py-2">
                        CANTIDAD (PZ)
                    </th>
                    <th scope="col" class="px-6 py-2">
                        PESO (KG)
                    </th>
                    <th scope="col" class="px-6 py-2">
                        COSTO UNIT.
                    </th>
                    <th scope="col" class="px-6 py-2">
                        IMPORTE
                    </th>
                    <th scope="col" class="px-6 py-2">
                        IVA
                    </th>
                    <th scope="col" class="px-6 py-2">
                        FECHA COMPRA
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orden_result as $index => $row)
                    <tr wire:key='{{ $index }}'
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <th class="px-3 py-2 text-center">
                            {{ $row['codigo_producto'] }}
                        </th>
                        <td class="px-6 py-2 uppercase">
                            {{ $row['nombre'] }}
                        </td>
                        <td class="px-6 py-2 uppercase">
                            <select id="proveedor" wire:model='orden_result.{{ $index }}.id_proveedor'
                                class=" bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option value="{{ null }}">Seleccionar</option>
                                @foreach ($this->proveedores as $index_prov => $prov)
                                    <option wire:key='{{ $index_prov }}' value="{{ $prov->id }}">
                                        {{ $prov->nombre }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-6 py-2 ">
                            <input wire:model='orden_result.{{ $index }}.cantidad' wire:change='calculateTable()'
                                type="number" min="0"
                                class="w-16 max-w-20 block p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </td>
                        <td class="px-6 py-2 ">
                            <input wire:model='orden_result.{{ $index }}.peso' wire:change='calculateTable()'
                                type="number" min="0"
                                class="w-16 max-w-20 block p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </td>
                        <td class="px-6 py-2">
                            <div class="flex gap-1 items-center">
                                $<input wire:model='orden_result.{{ $index }}.costo_unitario'
                                    wire:change='calculateTable()' type="number"
                                    class="block w-16 max-w-20 p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            </div>
                        </td>
                        <td class="px-6 py-2">
                            <div class="flex gap-1 items-center">
                                $<input wire:model='orden_result.{{ $index }}.importe' type="number"
                                    min="0"
                                    class="w-16 max-w-20 block p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            </div>
                        </td>
                        <td class="px-6 py-2">
                            <div class="flex gap-1 items-center">
                                $<input wire:model='orden_result.{{ $index }}.iva' type="number"
                                    class="block w-16 max-w-20 p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            </div>
                        </td>
                        <td class="px-6 py-2">
                            <input wire:model='orden_result.{{ $index }}.fecha_compra'type="date"
                                class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @error('orden_result')
            <div class="bg-red-100 border border-red-400 text-red-700 px-2">
                {{ $message }}
            </div>
        @enderror
    </div>
    {{-- BOTONES DE ACCION --}}
    <div>

        <button type="button" wire:click='aplicarEntrada'
            class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Aplicar
            Entrada
        </button>
        <a type="button" href="{{ route('almacen.entradas') }}"
            class="py-2.5 px-5 ms-3 text-sm font-medium text-red-900 focus:outline-none bg-red rounded-lg border border-red-200 hover:bg-red-100 hover:text-red-700 focus:z-10 focus:ring-4 focus:ring-red-100 dark:focus:ring-red-700 dark:bg-red-800 dark:text-red-400 dark:border-red-600 dark:hover:text-white dark:hover:bg-red-700">Cancelar
        </a>
    </div>
    {{-- LOADING SCREEN --}}
    <div wire:loading.delay wire:target='aplicarEntrada'>
        <x-loading-screen>
            <x-slot name='body'>
                <div class="flex">
                    <div class="me-4">
                        @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
                    </div>
                    <p>Guardando entrada...</p>
                </div>
            </x-slot>
        </x-loading-screen>
    </div>
    {{-- Action message --}}
    <x-action-message on='entrada'>
        @if (session('success-entrada'))
            <div id="alert-exito"
                class="flex items-center p-4 mb-4 text-green-800 border-t-4 border-green-300 bg-green-50 dark:text-green-400 dark:bg-gray-800 dark:border-green-800"
                role="alert">
                <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                </svg>
                <div class="ms-3 text-sm font-medium">
                    {{ session('success-entrada') }}
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
</div>
