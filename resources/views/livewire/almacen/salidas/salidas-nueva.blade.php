<div @keyup.ctrl.window="$dispatch('open-modal', {name:'modal-articulos'})">
    {{-- Contenido --}}
    <div class="py-5">
        <div class="flex ms-3">
            <div class="inline-flex flex-grow">
                <button x-data x-on:click="$dispatch('open-modal', { name: 'modal-articulos' })"
                    class="block w-full md:w-auto text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                    type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path fill-rule="evenodd"
                            d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <h4 class="flex items-center ms-2 text-2xl font-bold dark:text-white">NUEVA SALIDA</h4>
            </div>
            <div class="inline-flex">
                <label for="fecha"
                    class="flex items-center text-sm font-medium text-gray-900 dark:text-white">FECHA:</label>
                <input type="text" id="fecha" aria-label="fecha" wire:model='fechaActual'
                    class="ms-2 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    disabled>
            </div>
        </div>
    </div>
    <div class="inline-flex">
        {{-- ORIGEN DE SALIDA --}}
        <div class="col-span-1">
            <label for="origen" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Origen:</label>
            <select id="origen" wire:model='clave_origen' wire:change='changedOrigen'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option selected value="{{ null }}">SELECCIONAR</option>
                @foreach ($this->bodegas_origen as $item)
                    <option value="{{ $item->clave }}">{{ $item->descripcion }}</option>
                @endforeach>
            </select>
            @error('clave_origen')
                <x-input-error messages="{{ $message }}" />
            @enderror
        </div>
        {{-- SELECCIONAR EL DESTINO DE LA SALIDA --}}
        <div class="ms-3 w-fit">
            <label for="destino" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Destino:</label>
            <select id="destino" wire:model='clave_destino'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option selected value="{{ null }}">SELECCIONAR</option>
                @foreach ($this->bodegas_destino as $item)
                    <option value="{{ $item->clave }}">{{ $item->descripcion }}</option>
                @endforeach>
            </select>
            @error('clave_destino')
                <x-input-error messages="{{ $message }}" />
            @enderror
        </div>

        {{-- CONTIENE LAS POSIBLES OBSERVACIONES PARA AUTORIZAR UNA SALIDA --}}
        <form class="ms-3 w-96">
            <label for="observaciones"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nota:</label>
            <input type="text" wire:model='observaciones'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Observaciones">
            @error('observaciones')
                <x-input-error messages="{{ $message }}" />
            @enderror
        </form>
    </div>

    {{-- Tabla --}}
    <div class="ms-3 mx-3 my-3">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            CÓDIGO
                        </th>
                        <th scope="col" class="px-6 py-3 w-96">
                            DESCRIPCIÓN
                        </th>
                        <th scope="col" class="px-4 py-3">
                            CANTIDAD ORIGEN
                        </th>
                        <th scope="col" class="px-4 py-3">
                            PESO ORIGEN
                        </th>
                        <th scope="col" class="px-6 py-3">
                            CANTIDAD SALIDA
                        </th>
                        <th scope="col" class="px-6 py-3">
                            PESO SALIDA
                        </th>
                        <th scope="col" class="px-6 py-3">
                            PRECIO
                        </th>
                        <th scope="col" class="px-6 py-3">
                            MONTO
                        </th>
                        <th scope="col" class="px-6 py-3">
                            ACCIONES
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($articulos as $articuloIndex => $articulo)
                        <tr
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-2">
                                {{ $articulo['codigo'] }}
                            </td>
                            <td class="px-6 py-2">
                                {{ $articulo['nombre'] }}
                            </td>
                            <td class="px-6 py-2">
                                {{ $articulo['cantidad_origen'] ?: '' }}
                            </td>
                            <td class="px-6 py-2">
                                {{ $articulo['peso_origen'] ?: '' }}
                            </td>
                            <td class="px-6 py-2">
                                {{ $articulo['cantidad_salida'] }}
                            </td>
                            <td class="px-6 py-2">
                                {{ $articulo['peso_salida'] }}
                            </td>
                            <td class="px-6 py-2">
                                $ {{ $articulo['costo_unitario'] }}
                            </td>
                            <td class="px-6 py-2">
                                $ {{ $articulo['monto'] }}
                            </td>
                            <td class="px-6 py-2">
                                <button type="button" wire:click="remove({{ $articuloIndex }})"
                                    class="text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-3.5 py-1.5 text-center me-2  dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-800">
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
    </div>
    <div>
        @error('articulos')
            <x-input-error messages="{{ $message }}" />
        @enderror
    </div>

    {{--Butons Acept and Cancel--}}
    <div class="ms-3 mx-3">
        {{-- Linea divisora --}}
        <hr class="h-px my-1 bg-gray-300 border-0 dark:bg-gray-700">

        {{-- Boton de regresar --}}
        <a type="button" href="{{ route('almacen.salidas') }}"
            class="my-2 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
            <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 12h14M5 12l4-4m-4 4 4 4" />
            </svg>Regresar
        </a>
        <button type="button" wire:click='confirmarSalida'
            class="my-2 text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                height="24" fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M7.833 2c-.507 0-.98.216-1.318.576A1.92 1.92 0 0 0 6 3.89V21a1 1 0 0 0 1.625.78L12 18.28l4.375 3.5A1 1 0 0 0 18 21V3.889c0-.481-.178-.954-.515-1.313A1.808 1.808 0 0 0 16.167 2H7.833Z" />
            </svg>Confirmar Salida(s)
        </button>
    </div>
    {{-- Modal para dar salida a articulos --}}
    <x-modal name="modal-articulos" title="SALIDA DE ARTICULO">
        {{-- MODAL BODY --}}
        <x-slot:body>
            <livewire:almacen.salidas.modalSalidas wire:model='clave_origen' />
        </x-slot>
    </x-modal>
    <!--Alerts-->
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

    {{-- LOADING SCREEN --}}
    <div wire:loading.delay wire:target='confirmarSalida'>
        <x-loading-screen>
            <x-slot name='body'>
                <div class="flex">
                    <div class="me-4">
                        @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
                    </div>
                    <p>Guardando salida...</p>
                </div>
            </x-slot>
        </x-loading-screen>
    </div>
</div>
