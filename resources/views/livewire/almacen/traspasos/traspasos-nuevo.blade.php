<div @keyup.ctrl.window="$dispatch('open-modal', {name:'articulos-modal'})">
    {{-- Header --}}
    <div class="py-5">
        <div class="flex ms-3">
            {{-- Tittle and add button --}}
            <div class="inline-flex flex-grow">
                <button @click="$dispatch('open-modal', {name:'articulos-modal'})"
                    class="max-h-11 w-full md:w-auto text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                    type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path fill-rule="evenodd"
                            d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <h4 class="flex items-center ms-2 text-2xl font-bold dark:text-white">NUEVO TRASPASO DE ARTICULOS
                </h4>
            </div>
            {{-- Date --}}
            <div class="inline-flex">
                <label for="name" class="flex items-center text-sm font-medium text-gray-900 dark:text-white">FECHA:
                </label>
                <input type="text" aria-label="disabled input"
                    class="ms-2 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    value="{{ $today }}" disabled>
            </div>
        </div>
    </div>
    {{-- Observaciones --}}
    <div class="max-w-xl mx-3 mb-3">
        <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Observaciones</label>
        <input type="text" wire:model='observaciones'
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
    </div>
    {{-- Tabla --}}
    <div class="ms-3 mx-3">
        <div class="overflow-y-auto h-96">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            CÓDIGO
                        </th>
                        <th scope="col" class="px-6 py-3">
                            DESCRIPCIÓN
                        </th>
                        <th scope="col" class="px-6 py-3">
                            CANTIDAD
                        </th>
                        <th scope="col" class="px-6 py-3">
                            PESO
                        </th>
                        <th scope="col" class="px-4 py-3">
                            ORIGEN
                        </th>
                        <th scope="col" class="px-4 py-3">
                            EXISTENCIAS
                        </th>
                        <th scope="col" class="px-4 py-3">
                            DESTINO
                        </th>
                        <th scope="col" class="px-4 py-3">
                            EXISTENCIAS
                        </th>
                        <th scope="col" class="px-6 py-3">
                            ACCIONES
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lista_articulos as $index => $articulo)
                        <tr wire:key='{{ $index }}'
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-2">
                                {{ $articulo['codigo'] }}
                            </td>
                            <td class="px-6 py-2">
                                {{ $articulo['nombre'] }}
                            </td>
                            <td class="px-6 py-2">
                                {{ $articulo['cantidad'] }}
                            </td>
                            <td class="px-6 py-2">
                                {{ $articulo['peso'] }}
                            </td>
                            <td class="px-6 py-2">
                                {{ $this->bodegas->find($articulo['clave_bodega_origen'])->descripcion }}
                            </td>
                            <td class="px-6 py-2">
                                @foreach ($articulo['existencias_origen'] as $key_stock => $stock)
                                    <p>{{ $key_stock }}:{{ $stock }}</p>
                                @endforeach
                            </td>
                            <td class="px-6 py-2">
                                {{ $this->bodegas->find($articulo['clave_bodega_destino'])->descripcion }}
                            </td>
                            <td class="px-6 py-2">
                                @foreach ($articulo['existencias_destino'] as $key_stock => $stock)
                                    <p>{{ $key_stock }}:{{ $stock }}</p>
                                @endforeach
                            </td>
                            <td class="px-6 py-2">
                                <button type="button" wire:click='removerArticulo({{ $index }})'
                                    class="text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-3.5 py-1.5 text-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                        class="w-5 h-5">
                                        <path fill-rule="evenodd"
                                            d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @error('lista_articulos')
            <x-input-error messages="{{ $message }}" />
        @enderror
    </div>

    {{-- BOTONES DE ACCION --}}
    <div class="my-3">
        <button type="button" wire:click='aplicarTraspaso'
            class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Aplicar
            traspaso
        </button>
        <a type="button" href="{{ route('almacen.traspasos') }}"
            class="py-2.5 px-5 ms-3 text-sm font-medium text-red-900 focus:outline-none bg-red rounded-lg border border-red-200 hover:bg-red-100 hover:text-red-700 focus:z-10 focus:ring-4 focus:ring-red-100 dark:focus:ring-red-700 dark:bg-red-800 dark:text-red-400 dark:border-red-600 dark:hover:text-white dark:hover:bg-red-700">Cancelar
        </a>
    </div>

    {{-- Modal --}}
    <x-modal title="Agregar articulo" name="articulos-modal">
        <x-slot name="body">
            <!-- Modal body -->
            <div>
                <div class="grid gap-4 mb-4 grid-cols-4">
                    {{-- Search bar --}}
                    <div class="col-span-4">
                        <livewire:search-input :params="[
                            'tittle_bar' => 'Codigo o nombre articulo',
                            'table_name' => 'catalogo_vista_verde',
                            'table_columns' => ['codigo', 'nombre'],
                            'primary_key' => 'codigo',
                            'event' => 'selected-articulo',
                            'args' => 'INV%',
                        ]" />
                    </div>
                    {{-- Codigo --}}
                    <div class="col-span-1 sm:col-span-1">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Código</label>
                        <input type="text" aria-label="disabled input"
                            value="{{ $articulo_seleccionado ? $articulo_seleccionado['codigo'] : '' }}"
                            class="mb-1 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            disabled>
                    </div>
                    {{-- Nombre --}}
                    <div class="col-span-1 sm:col-span-3">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre</label>
                        <input type="text" aria-label="disabled input"
                            value="{{ $articulo_seleccionado ? $articulo_seleccionado['nombre'] : '' }}"
                            class="mb-1 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            disabled>
                        @error('articulo_seleccionado')
                            <div class="text-red-500 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- Origen --}}
                    <div class="col-span-1 sm:col-span-1 ">
                        <label class="flex items-center text-sm font-medium text-gray-900 dark:text-white">Origen:
                        </label>
                        <select wire:model.live='origen_seleccionado' wire:target='origen_seleccionado'
                            wire:loading.class='cursor-not-allowed' wire:loading.attr='disabled'
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option selected value="{{ null }}">Seleccionar</option>
                            @foreach ($this->bodegas as $index_bodega => $bodega)
                                <option value="{{ $bodega->clave }}">{{ $bodega->descripcion }}</option>
                            @endforeach
                        </select>
                        @error('origen_seleccionado')
                            <div class="text-red-500 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- Stock --}}
                    <div class="col-span-1 sm:col-span-1 mt-4">
                        <div wire:loading.remove wire:target='origen_seleccionado'>
                            <div class="flex gap-2">
                                <p class="font-semibold text-sm">Stock unitario:</p>
                                <p>{{ $stock_origen_cantidad ? $stock_origen_cantidad[$origen_seleccionado] : '' }}
                                </p>
                            </div>
                            <div class="flex gap-2">
                                <p class="font-semibold text-sm">Stock peso:</p>
                                <p>{{ $stock_origen_peso ? $stock_origen_peso[$origen_seleccionado] : '' }}</p>
                            </div>
                        </div>
                    </div>
                    {{-- Destino --}}
                    <div class="col-span-1 sm:col-span-1">
                        <label class="flex items-center text-sm font-medium text-gray-900 dark:text-white">Destino:
                        </label>
                        <select wire:model.live='destino_seleccionado' wire:target='destino_seleccionado'
                            wire:loading.class='cursor-not-allowed' wire:loading.attr='disabled'
                            class=" bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option selected value="{{ null }}">Seleccionar</option>
                            @foreach ($this->bodegas as $index_bodega => $bodega)
                                <option value="{{ $bodega->clave }}">{{ $bodega->descripcion }}</option>
                            @endforeach
                        </select>
                        @error('destino_seleccionado')
                            <div class="text-red-500 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- Stock --}}
                    <div class="col-span-1 sm:col-span-1 mt-4">
                        <div wire:loading.remove wire:target='destino_seleccionado'>
                            <div class="flex gap-2">
                                <p class="font-semibold text-sm">Stock unitario:</p>
                                <p>{{ $stock_destino_cantidad ? $stock_destino_cantidad[$destino_seleccionado] : '' }}
                                </p>
                            </div>
                            <div class="flex gap-2">
                                <p class="font-semibold text-sm">Stock peso:</p>
                                <p>{{ $stock_destino_peso ? $stock_destino_peso[$destino_seleccionado] : '' }}</p>
                            </div>
                        </div>
                    </div>
                    {{-- Cantidad --}}
                    <div class="col-span-1 sm:col-span-1">
                        <label for="st_min"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cantidad</label>
                        <input type="number" name="st_min" wire:model='cantidad'
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Unitario">
                        @error('cantidad')
                            <div class="text-red-500 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- Peso --}}
                    <div class="col-span-1 sm:col-span-1">
                        <label for="st_min"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Peso</label>
                        <input type="number" name="st_min" wire:model='peso'
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Peso">
                    </div>
                </div>
            </div>
            <!-- Modal footer -->
            <div>
                <button type="button" wire:click='agregarArticulo'
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Agregar
                </button>
                <button type="button" wire:click='closeModal'
                    class="py-2.5 px-5 ms-3 text-sm font-medium text-red-900 focus:outline-none bg-red rounded-lg border border-red-200 hover:bg-red-100 hover:text-red-700 focus:z-10 focus:ring-4 focus:ring-red-100 dark:focus:ring-red-700 dark:bg-red-800 dark:text-red-400 dark:border-red-600 dark:hover:text-white dark:hover:bg-red-700">Cancelar
                </button>
            </div>
        </x-slot>
    </x-modal>

    {{-- LOADING SCREEN --}}
    <div wire:loading wire:target='aplicarTraspaso'>
        <x-loading-screen>
            <x-slot name='body'>
                <div class="flex">
                    <div class="me-4">
                        @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
                    </div>
                    <p>Aplicando traspaso...</p>
                </div>
            </x-slot>
        </x-loading-screen>
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
</div>
