<div @keyup.ctrl.window="$dispatch('open-modal', {name:'modal-insumo'})">
    {{-- Titulo y boton --}}
    <div class="container py-2">
        <div class="flex ms-3">
            <button x-data x-on:click="$dispatch('open-modal', {name:'modal-insumo'})"
                class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-2xl text-sm px-3 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                type="button">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
            </button>
            <h4 class="flex items-center ms-2 text-2xl font-bold dark:text-white">NUEVA PRODUCCION DE INSUMOS</h4>
        </div>
    </div>
    {{-- Barra de acciones --}}
    <div class="flex justify-between items-end px-3 mb-3">
        <div class="flex gap-2 items-end">
            {{-- BODEGA ORIGEN --}}
            <div>
                <label for="bodega" class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Bodega
                    origen</label>
                <select id="bodega" disabled wire:model='clave_origen'
                    class="w-fit cursor-not-allowed bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block  p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option selected value="{{ null }}">BODEGA</option>
                    @foreach ($this->bodegas as $index => $item)
                        <option wire:key='{{ $index }}' value="{{ $item->clave }}">
                            {{ $item->descripcion }}
                        </option>
                    @endforeach
                </select>
            </div>
            {{-- BODEGA DESTINO --}}
            <div>
                <label for="bodega" class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Bodega
                    destino</label>
                <select id="bodega" disabled wire:model='clave_destino'
                    class="w-fit cursor-not-allowed bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option selected value="{{ null }}">BODEGA</option>
                    @foreach ($this->bodegas as $index => $item)
                        <option wire:key='{{ $index }}' value="{{ $item->clave }}">
                            {{ $item->descripcion }}
                        </option>
                    @endforeach
                </select>
            </div>
            {{-- OBSERVACIONES --}}
            <div>
                <input type="text" wire:model='observaciones'
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block  p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Observaciones" />
            </div>
        </div>
        <div class="flex gap-2 items-end">
            {{-- FECHA --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Fecha</label>
                <input type="date" wire:model='fecha_existencias' disabled
                    class="opacity-60 cursor-not-allowed w-fit bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            </div>
            {{-- HORA --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Hora</label>
                <input type="time" wire:model='hora_existencias' disabled
                    class=" opacity-60 cursor-not-allowed w-fit bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            </div>
        </div>
    </div>
    {{-- TABLA RESULTADOS --}}
    <div class="px-3 relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
            wire:loading.class='animate-pulse' wire:target='editableReceta,eliminarArticulo'>
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-3 py-2">
                        #
                    </th>
                    <th scope="col" class="px-3 py-2">
                        INSUMO ELABORADO
                    </th>
                    <th scope="col" class="px-3 py-2">
                        STOCK DESTINO
                    </th>
                    <th scope="col" class="px-3 py-2 w-16">
                        CANTIDAD
                    </th>
                    <th scope="col" class="px-3 py-2">
                        REND.RECETA
                    </th>
                    <th scope="col" class="px-3 py-2">
                        TOTAL ELABORADO
                    </th>
                    <th scope="col" class="px-3 py-2">
                        UNIDAD
                    </th>
                    <th scope="col" class="px-3 py-2">
                        ACCIONES
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->form->insumos_elaborados as $i => $insumo)
                    <tr wire:key='{{ $i }}'
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <th scope="row"
                            class="px-3 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $insumo['clave'] }}
                        </th>
                        <td class="px-3 py-2">
                            {{ $insumo['descripcion'] }}
                        </td>
                        <td class="px-3 py-2">
                            {{ number_format($insumo['movimientos_almacen_sum_cantidad_insumo'] ?? 0, 3) }}
                        </td>
                        <td class="px-3 py-2 w-16">
                            <input type="number" wire:model="form.insumos_elaborados.{{ $i }}.cantidad"
                                wire:change='calcularTotalElaborado({{ $i }})'
                                class="block w-full p-1.5 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </td>
                        <td class="px-3 py-2">
                            <button type="button" wire:click='editableReceta({{ $i }})'
                                class="text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-4 py-1.5 text-center inline-flex items-center me-2 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="me-2 w-5 h-5">
                                    <path
                                        d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z" />
                                    <path
                                        d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z" />
                                </svg>
                                {{ $insumo['rendimiento_elaborado'] }}
                            </button>
                        </td>
                        <td class="px-3 py-2">
                            {{ $insumo['total_elaborado'] }}
                        </td>
                        <td class="px-3 py-2">
                            {{ $insumo['unidad']['descripcion'] }}
                        </td>
                        <td class="px-3 py-2">
                            <button type="button" wire:click ='eliminarArticulo({{ $i }})'
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

    {{-- BOTONES --}}
    <div>
        {{-- BOTON DE CANCELAR --}}
        <a type="button" href="{{ route('almacen.produccion') }}"
            class="my-2 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
            <svg class="w-5 h-5 me-2 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 18 17.94 6M18 18 6.06 6" />
            </svg>
            Cancelar
        </a>
        {{-- BOTON DE GUARDAR --}}
        <button type="button" wire:click='guardar'
            class="my-2 text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
            <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                height="24" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd"
                    d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7.414A2 2 0 0 0 20.414 6L18 3.586A2 2 0 0 0 16.586 3H5Zm10 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM8 7V5h8v2a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1Z"
                    clip-rule="evenodd" />
            </svg>
            Guardar
        </button>
    </div>
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
    <!--INDICADOR DE CARGA-->
    <div wire:loading wire:target='guardar'>
        <x-loading-screen name='loading'>
            <x-slot name='body'>
                <div class="flex">
                    <div class="me-4">
                        @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
                    </div>
                    <p>Procesando Produccion . . .</p>
                </div>
            </x-slot>
        </x-loading-screen>
    </div>

    {{-- MODAL INSUMO ELABORADO --}}
    <x-modal name="modal-insumo" title="AÑADIR INSUMO ELABORADO">
        <x-slot name='body'>
            <!-- Modal content -->
            <div class="h-auto max-w-3xl overflow-y-auto">
                <!-- Modal body -->
                <div class="p-1 w-full max-w-3xl max-h-full">
                    <div>
                        {{-- elementos principales --}}
                        <div class="grid grid-cols-4 gap-3">
                            {{-- BODEGA DE ORIGEN --}}
                            <div>
                                <select tabindex="-1"id="b_origen" wire:model='clave_origen'
                                    class="{{ $locked_bodegas ? 'pointer-events-none opacity-50' : '' }} bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option selected value="{{ null }}">ORIGEN</option>
                                    @foreach ($this->bodegas as $b)
                                        <option value="{{ $b->clave }}">{{ $b->descripcion }}</option>
                                    @endforeach
                                </select>
                                @error('clave_origen')
                                    <x-input-error messages="{{ $message }}" />
                                @enderror
                            </div>
                            {{-- BODEGA DE DESTINO --}}
                            <div>
                                <select tabindex="-1" id="b_destino" wire:model='clave_destino'
                                    class="{{ $locked_bodegas ? 'pointer-events-none opacity-50' : '' }} bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option selected value="{{ null }}">DESTINO</option>
                                    @foreach ($this->bodegas as $b)
                                        <option value="{{ $b->clave }}">{{ $b->descripcion }}</option>
                                    @endforeach
                                </select>
                                @error('clave_destino')
                                    <x-input-error messages="{{ $message }}" />
                                @enderror
                            </div>
                            {{-- FECHA --}}
                            <div>
                                <input tabindex="-1" type="date" wire:model='fecha_existencias'
                                    class="{{ $locked_bodegas ? 'pointer-events-none opacity-50' : '' }} w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                @error('fecha_existencias')
                                    <x-input-error messages="{{ $message }}" />
                                @enderror
                            </div>
                            {{-- HORA --}}
                            <div>
                                <input tabindex="-1" type="time" wire:model='hora_existencias'
                                    class="{{ $locked_bodegas ? 'pointer-events-none opacity-50' : '' }} w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                @error('hora_existencias')
                                    <x-input-error messages="{{ $message }}" />
                                @enderror
                            </div>
                        </div>
                        {{-- Barra de busqueda --}}
                        <div class="relative mt-2">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg wire:loading.delay.remove wire:target="search_insumo_elaborado"
                                    class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                                <!--Loading indicator-->
                                <div wire:loading.delay wire:target='search_insumo_elaborado'>
                                    @include('livewire.utils.loading', ['w' => 5, 'h' => 5])
                                </div>
                            </div>
                            <input type="text" wire:model.live.debounce.500ms="search_insumo_elaborado"
                                class="w-full p-1.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Código o Descripción" />
                        </div>
                    </div>
                    <!-- Result table-->
                    <div class="overflow-y-auto h-80 my-2">
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
                                        UNIDAD
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($this->insumos as $row)
                                    <tr wire:key='{{ $row->clave }}'
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600">
                                        <td class="w-4 p-4">
                                            <input id="checkbox-{{ $row->clave }}" type="checkbox"
                                                wire:model="insumos_elaborados.{{ $row->clave }}"
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
                                            {{ $row->unidad->descripcion }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Modal footer -->
                <div>
                    <button type="button" wire:click='finalizarSeleccion'
                        class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 inline-flex items-center text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Aceptar
                        <!--Loading indicator-->
                        <div class="flex justify-center ms-2" wire:loading.delay wire:target='finalizarSeleccion'>
                            @include('livewire.utils.loading', ['w' => 4, 'h' => 4])
                        </div>
                    </button>
                </div>
            </div>
        </x-slot>
    </x-modal>

    {{-- MODAL ADVERTENCIA --}}
    <x-modal name="modal-advertencia" title="PRODUCCION FALTANTE">
        <x-slot name='body'>
            <!-- Modal content -->

        </x-slot>
    </x-modal>

    {{-- MODAL RECETA INSUMO --}}
    <x-modal name="modal-receta" title="EDITAR: {{ $insumo_editable ? $insumo_editable['descripcion'] : '' }}">
        <x-slot name='body'>
            <!-- Modal content -->
            <div class="h-auto max-w-4xl overflow-y-auto">
                <!-- Modal body -->
                <div class="p-1 w-full max-w-4xl max-h-full">
                    {{-- BARRA DE BUSQUEDA (componente) --}}
                    <livewire:search-bar tittle="Buscar insumo" table="insumos" :columns="['clave', 'descripcion']" primary="clave"
                        event="selected-insumo" :conditions="[['inventariable', '=', 1]]" />
                    <!-- Result table-->
                    <div class="overflow-y-auto h-80 my-2">
                        <table class=" w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="p-3">
                                    </th>
                                    <th scope="col" class="px-3 py-3">
                                        #
                                    </th>
                                    <th scope="col" class="px-3 py-3">
                                        INSUMO REQUERIDO
                                    </th>
                                    <th scope="col" class="px-3 py-3">
                                        STOCK ORIGEN
                                    </th>
                                    <th scope="col" class="px-3 py-3">
                                        C.SIN MERMA
                                    </th>
                                    <th scope="col" class="px-3 py-3">
                                        C.CON MERMA
                                    </th>
                                    <th scope="col" class="px-3 py-3">
                                        UNIDAD
                                    </th>
                                    <th scope="col" class="px-3 py-3">
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($insumos_receta as $i => $row)
                                    <tr wire:key='{{ $i }}'
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600">
                                        <td class="p-3">
                                            <input id="checkbox-{{ $row['clave_insumo'] }}" type="checkbox"
                                                wire:model="insumos_receta.{{ $i }}.selected"
                                                class="w-6 h-6 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        </td>
                                        <th scope="row"
                                            class="px-3 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $row['clave_insumo'] }}
                                        </th>
                                        <td class="w-80 font-medium text-gray-900  dark:text-white">
                                            <div class="flex items-center">
                                                <label for="checkbox-{{ $row['clave_insumo'] }}"
                                                    class="w-96 py-4 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                                    {{ $row['ingrediente']['descripcion'] }}
                                                </label>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 font-medium w-32 text-gray-900 dark:text-white">
                                            {{ number_format($row['existencias_origen'], 3) }}
                                        </td>
                                        <td class="px-3 py-3 font-medium w-32 text-gray-900 dark:text-white">
                                            <input type="number" step="0.001"
                                                wire:model='insumos_receta.{{ $i }}.cantidad'
                                                class="block w-full  p-1.5 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            @error('insumos_receta.' . $i . '.cantidad')
                                                <x-input-error messages="{{ $message }}" />
                                            @enderror
                                        </td>
                                        <td class="px-3 py-3 font-medium w-32 text-gray-900 dark:text-white">
                                            <input type="number" step="0.001"
                                                wire:model='insumos_receta.{{ $i }}.cantidad_c_merma'
                                                wire:change='calcularTotal({{ $i }})'
                                                class="block w-full p-1.5 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            @error('insumos_receta.' . $i . '.cantidad_c_merma')
                                                <x-input-error messages="{{ $message }}" />
                                            @enderror
                                        </td>
                                        <td class="px-3 py-3 font-medium w-fit text-gray-900 dark:text-white">
                                            {{ $row['ingrediente']['unidad']['descripcion'] }}
                                        </td>
                                        <td class="px-3 py-3 w-fit text-gray-900 dark:text-white">
                                            <button type="button" wire:click='eliminarReceta({{ $i }})'
                                                class="text-gray-700 border border-gray-700 hover:bg-gray-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm p-1.5 text-center inline-flex items-center dark:border-gray-500 dark:text-gray-500 dark:hover:text-white dark:focus:ring-gray-800 dark:hover:bg-gray-500">
                                                <svg class="w-5 h-5" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path fill-rule="evenodd"
                                                        d="M8.586 2.586A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h3a1 1 0 1 1 0 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8a1 1 0 0 1 0-2h3V4a2 2 0 0 1 .586-1.414ZM10 6h4V4h-4v2Zm1 4a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Zm4 0a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Z"
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
                <!-- Modal footer -->
                <div>
                    <button type="button" wire:click='aceptarEdicion'
                        class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 inline-flex items-center text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Aceptar
                        <!--Loading indicator-->
                        <div class="flex justify-center ms-2" wire:loading.delay wire:target='finalizarSeleccion'>
                            @include('livewire.utils.loading', ['w' => 4, 'h' => 4])
                        </div>
                    </button>
                </div>
            </div>
        </x-slot>
    </x-modal>
</div>
