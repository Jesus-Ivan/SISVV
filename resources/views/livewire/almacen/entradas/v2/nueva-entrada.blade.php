<div class="p-2" @keyup.ctrl.window="$dispatch('open-modal', {name:'modal-articulos'})">
    {{-- Tittle and button --}}
    <div>
        <div class="inline-flex flex-grow">
            <button x-data x-on:click="$dispatch('open-modal', {name:'modal-articulos'})"
                class="w-fit text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                type="button">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                    <path fill-rule="evenodd"
                        d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z"
                        clip-rule="evenodd" />
                </svg>
            </button>
            <h4 class="flex items-center ms-2 text-2xl font-bold dark:text-white">NUEVA ENTRADA v2</h4>
        </div>
    </div>
    {{-- Search bar --}}
    <div class="flex py-3 gap-3">
        <div>
            {{-- Select de bodega --}}
            <select id="bodega" wire:model.live='clave_bodega' id="disabled-input" aria-label="disabled input"
                disabled
                class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-fit p-1.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option selected value="{{ null }}">BODEGA</option>
                @foreach ($this->bodegas as $b)
                    <option value="{{ $b->clave }}">{{ $b->descripcion }}</option>
                @endforeach
            </select>

            @error('clave_bodega')
                <x-input-error messages="{{ $message }}" />
            @enderror
        </div>
        <div>
            <input type="date" wire:model='fecha'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="0.0" />
            @error('fecha')
                <x-input-error messages="{{ $message }}" />
            @enderror
        </div>
        <div>
            <input type="time" wire:model='hora'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="0.0" />
            @error('hora')
                <x-input-error messages="{{ $message }}" />
            @enderror
        </div>
        {{-- proveedor general --}}
        <div>
            <select wire:change='actualizarProveedor($event.target.value)'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="{{ null }}" selected>Proveedor...</option>
                @foreach ($this->proveedores as $p)
                    <option wire:key='{{ $p->id }}' value="{{ $p->id }}">{{ $p->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex grow justify-end">
            <input type="text" wire:model='observaciones'
                class="h-9 max-w-md bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Observaciones ..." />
        </div>

    </div>
    {{-- Tabla de resultados --}}
    <div class="relative overflow-y-auto shadow-md sm:rounded-lg h-96">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-3 py-2">
                        #
                    </th>
                    <th scope="col" class="px-3 py-2">
                        Descripcion
                    </th>
                    <th scope="col" class="px-3 py-2">
                        Proveedor
                    </th>
                    <th scope="col" class="px-3 py-2">
                        Cantidad
                    </th>
                    <th scope="col" class="px-3 py-2">
                        Unidad
                    </th>
                    <th scope="col" class="px-3 py-2">
                        Costo Unitario
                    </th>
                    <th scope="col" class="px-3 py-2">
                        IVA
                    </th>
                    <th scope="col" class="px-3 py-2">
                        C.C.Impuesto
                    </th>
                    <th scope="col" class="px-3 py-2">
                        Subtotal
                    </th>
                    <th scope="col" class="px-3 py-2">
                        <span class="sr-only">Acciones</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($articulos_table as $index => $item)
                    <tr wire:key='{{ $index }}'
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <th scope="row"
                            class="px-3 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $item['clave'] }}
                        </th>
                        <td class="px-3 py-2">
                            {{ $item['descripcion'] }}
                        </td>
                        <td class="px-3 py-2">
                            <select wire:model='articulos_table.{{ $index }}.id_proveedor'
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option selected value="{{ null }}">Seleccione</option>
                                @foreach ($this->proveedores as $i => $p)
                                    <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-3 py-2">
                            <input type="number" wire:model='articulos_table.{{ $index }}.cantidad'
                                wire:change='actualizarImporte({{ $index }})'
                                class="max-w-24 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block  p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="0.0" />
                        </td>
                        <td class="px-3 py-2">
                            {{ $item['unidad'] ? $item['unidad']['descripcion'] : '' }}
                        </td>
                        <td class="px-3 py-2 flex items-center">
                            $
                            <input type="number" wire:model='articulos_table.{{ $index }}.costo'
                                wire:change='updateCostoIva({{ $index }})'
                                class="max-w-28 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block  p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="0.0" />
                        </td>
                        <td class="px-3 py-2">
                            <input type="number" wire:model='articulos_table.{{ $index }}.iva'
                                wire:change='updateCostoIva({{ $index }})'
                                class="max-w-16 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block  p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="0.0" />
                        </td>
                        <td class="px-3 py-2 flex items-center">
                            $
                            <input type="number" wire:model='articulos_table.{{ $index }}.costo_con_impuesto'
                                wire:change='updateCostoSinIva({{ $index }})'
                                class="max-w-28 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block  p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="0.0" />
                        </td>
                        <td class="px-3 py-2">
                            ${{ number_format($item['importe'], 2) }}
                        </td>
                        <td class="px-3 py-2 text-right">
                            <button type="button" wire:click ='eliminarArticulo({{ $index }})'
                                class="text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-3.5 py-1.5 text-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="w-5 h-5">
                                    <path fill-rule="evenodd"
                                        d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Modal de articulos --}}
    <x-modal name="modal-articulos" title="AÑADIR INSUMO/PRESENTACION">
        <x-slot name='body'>
            <!-- Modal content -->
            <div class="h-auto max-w-4xl overflow-y-auto">
                <!-- Modal body -->
                <div class="p-1 w-full max-w-2xl max-h-full">
                    {{-- Barra de busqueda --}}
                    <div class="relative">
                        <div class="inline-flex gap-2">
                            {{-- Folio requi --}}
                            <input type="text" wire:keyup.enter='buscarRequisicion'
                                class="{{ $locked_bodega ? 'cursor-not-allowed pointer-events-none' : '' }} bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Folio requisicion" wire:model='folio_requi' />
                            {{-- Select de bodega --}}
                            <select id="bodega" wire:model='clave_bodega' wire:change ='actualizarItems'
                                class="{{ $locked_bodega ? 'cursor-not-allowed pointer-events-none' : '' }}  bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option selected value="{{ null }}">BODEGA</option>
                                @foreach ($this->bodegas as $b)
                                    <option value="{{ $b->clave }}">{{ $b->descripcion }}</option>
                                @endforeach
                            </select>
                            {{-- Input search --}}
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <svg wire:loading.delay.remove wire:target='search_input'
                                        class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                    </svg>
                                    <!--Loading indicator-->
                                    <div wire:loading.delay wire:target='search_input'>
                                        @include('livewire.utils.loading', ['w' => 5, 'h' => 5])
                                    </div>
                                </div>

                                <input type="text" wire:model.live.debounce.500ms="search_input"
                                    class="w-96 p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Código o Descripción" />
                            </div>
                        </div>
                    </div>
                    <!-- Result table-->
                    <div class="overflow-y-auto h-80 my-2" wire:loading.class='animate-pulse'
                        wire:target='actualizarItems, buscarRequisicion'>
                        <table class=" w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="p-4">
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        CODIGO
                                    </th>
                                    <th scope="col" class="py-3">
                                        DESCRIPCIÓN
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        C.C.IVA
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($this->articulos as $row)
                                    <tr wire:key='{{ $row->clave }}'
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600">
                                        <td class="w-4 p-4">
                                            <input id="checkbox-{{ $row->clave }}" type="checkbox"
                                                wire:model="selectedItems.{{ $row->clave }}"
                                                class="w-6 h-6 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        </td>
                                        <th scope="row"
                                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $row->clave }}
                                        </th>
                                        <td class="w-96 font-medium text-gray-900  dark:text-white">
                                            <div class="flex items-center">
                                                <label for="checkbox-{{ $row->clave }}"
                                                    class="w-96 py-4 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                                    {{ $row->descripcion }}
                                                </label>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 font-medium w-fit text-gray-900 dark:text-white">
                                            ${{ $row->costo_con_impuesto }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Modal footer -->
                <div
                    class="mt-2 flex items-center space-x-3 rtl:space-x-reverse border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button wire:click='agregarArticulos()' type="button"
                        class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Aceptar
                    </button>
                    <button wire:click='cancelar' type="button"
                        class="py-2.5 px-5 ms-3 text-sm font-medium text-red-900 focus:outline-none bg-red rounded-lg border border-red-200 hover:bg-red-100 hover:text-red-700 focus:z-10 focus:ring-4 focus:ring-red-100 dark:focus:ring-red-700 dark:bg-red-800 dark:text-red-400 dark:border-red-600 dark:hover:text-white dark:hover:bg-red-700">Cancelar
                    </button>
                </div>
            </div>
        </x-slot>
    </x-modal>
    {{-- BOTONES DE ACCION --}}
    <div>

        <button type="button" wire:click='aplicarEntrada'
            class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Aplicar
            Entrada
        </button>
        <a type="button" href="{{ route('almacen.entradav2') }}"
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
