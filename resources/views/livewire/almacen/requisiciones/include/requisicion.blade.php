<div @keyup.ctrl.window="$dispatch('open-modal', {name:'modal-articulos'})">
    {{-- Contenido --}}
    <div class="p-2.5">
        <div class="flex ">
            {{-- BOTON DE AGREGAR ARTICULO Y TITULO --}}
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
                <h4 class="flex items-center ms-2 text-2xl font-bold dark:text-white">{{ $tittle }}</h4>
            </div>
            {{-- FECHA Y FOLIO --}}
            <div class="inline-flex">
                <label for="name" class="flex items-center text-sm font-medium text-gray-900 dark:text-white">FECHA:
                </label>
                <input type="text" id="disabled-input" aria-label="disabled input"
                    class="ms-2 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full  cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    value="{{ $hoy }}" disabled>
            </div>
        </div>
    </div>
    {{-- BAR TIPO ORDEN --}}
    <div class="container py-2">
        <div class="flex ms-3">
            <div class="inline-flex flex-grow gap-3 items-end">
                {{-- TIPO DE ORDEN --}}
                <div>
                    <label for="departamento"
                        class="flex items-center mb-1 text-lg font-medium text-gray-900 dark:text-white">Tipo de
                        orden:</label>
                    <select id="departamento" wire:model='form.tipo_orden'
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 w-fit p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                        <option selected value="{{ null }}">Seleccionar Tipo</option>
                        <option value="G">General</option>
                        <option value="E">Evento</option>
                    </select>
                    @error('form.tipo_orden')
                        <x-input-error messages="{{ $message }}" />
                    @enderror
                </div>
                {{-- BOTON DE GUARDAR ORDEN --}}
                <button type="button" wire:click='guardarOrden()'
                    class="h-11 px-5 py-2.5 text-sm font-medium text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    <svg wire:loading.remove wire:target='guardarOrden' xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path
                            d="M19.5 21a3 3 0 0 0 3-3v-4.5a3 3 0 0 0-3-3h-15a3 3 0 0 0-3 3V18a3 3 0 0 0 3 3h15ZM1.5 10.146V6a3 3 0 0 1 3-3h5.379a2.25 2.25 0 0 1 1.59.659l2.122 2.121c.14.141.331.22.53.22H19.5a3 3 0 0 1 3 3v1.146A4.483 4.483 0 0 0 19.5 9h-15a4.483 4.483 0 0 0-3 1.146Z" />
                    </svg>
                    <!--Loading indicator-->
                    <div wire:loading wire:target='guardarOrden'>
                        @include('livewire.utils.loading', ['w' => 5, 'h' => 5])
                    </div>
                    Guardar orden
                </button>
            </div>
        </div>
    </div>

    {{-- Tabla --}}
    <div>
        <div class="ms-3 mx-3">
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-2 py-3">
                                CLAVE
                            </th>
                            <th scope="col" class="px-2 py-3 min-w-32">
                                DESCRIPCIÓN
                            </th>
                            <th scope="col" class="px-2 py-3">
                                PROVEEDOR
                            </th>
                            <th scope="col" class="px-2 py-3">
                                CANTIDAD
                            </th>
                            <th scope="col" class="px-2 py-3">
                                COSTO UNITARIO
                            </th>
                            <th scope="col" class="px-2 py-3">
                                IVA (%)
                            </th>
                            <th scope="col" class="px-2 py-3">
                                COSTO C/IMPUESTO
                            </th>
                            <th scope="col" class="px-2 py-3">
                                IMPORTE
                            </th>
                            <th scope="col" class="px-2 py-3">
                                ACCIONES
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->form->presentaciones as $index => $articulo)
                            @if (!array_key_exists('deleted', $articulo))
                                <tr wire:key='{{ $index }}'
                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-2 py-2">
                                        {{ $articulo['clave'] }}
                                    </td>
                                    <td class="px-2 py-2">
                                        {{ $articulo['descripcion'] }}
                                    </td>
                                    <td class="px-2 py-2">
                                        {{ $this->proveedores->find($articulo['id_proveedor'])->nombre }}
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="number"
                                            wire:model="form.presentaciones.{{ $index }}.cantidad"
                                            wire:change='actualizarImporte({{ $index }})'
                                            class="block w-16 p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    </td>
                                    <td class="px-2 py-2 flex gap-2 items-center">
                                        $<input type="number"
                                            wire:model="form.presentaciones.{{ $index }}.costo_unitario"
                                            wire:change='actualizarCostoIva({{ $index }})'
                                            class="block w-20 p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="number" wire:model="form.presentaciones.{{ $index }}.iva"
                                            wire:change='actualizarCostoIva({{ $index }})'
                                            class="block w-16 p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    </td>
                                    <td class="px-2 py-2 flex gap-2 items-center">
                                        $<input type="number"
                                            wire:model="form.presentaciones.{{ $index }}.costo_con_impuesto"
                                            wire:change='actualizarCostoSinIva({{ $index }})'
                                            class="block w-20 p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    </td>
                                    <td class="px-2 py-2">
                                        ${{ $articulo['importe'] }}
                                    </td>
                                    <td class="px-2 py-2">
                                        <button type="button" wire:click ="eliminarArticulo({{ $index }})"
                                            class="text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-3.5 py-1.5 text-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                fill="currentColor" class="w-5 h-5">
                                                <path fill-rule="evenodd"
                                                    d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @error('lista_articulos')
            <x-input-error messages="{{ $message }}" />
        @enderror
    </div>

    <!--Modal -->
    <x-modal name="modal-articulos" title="AÑADIR PRESENTACION">
        <x-slot name='body'>
            <!-- Modal content -->
            <div class="h-auto max-w-4xl overflow-y-auto">
                <!-- Modal body -->
                <div class="grid gap-2 grid-cols-3">
                    {{-- Grupo de Presentaciones --}}
                    <div class="col-span-1">
                        <select id="proveedor" wire:model.live='id_grupo'
                            class=" bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option selected value='{{ null }}'>Seleccionar Grupo</option>
                            @foreach ($this->grupos as $index_grupo => $grupo)
                                <option wire:key='{{ $index_grupo }}' value="{{ $grupo->id }}">
                                    {{ $grupo->descripcion }}</option>
                            @endforeach
                        </select>
                        @error('id_grupo')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                    {{-- BARRA BUSQUEDA ARTICULO --}}
                    <div class="col-span-2">
                        <livewire:search-bar tittle="Codigo o nombre" table="presentaciones" :columns="['clave', 'descripcion']"
                            primary="clave" event="selected-presentacion" :conditions="[['estado', '=', true], ['id_grupo', '=', $id_grupo]]" />
                    </div>
                    {{-- NOMBRE DEL ARTICULO --}}
                    <div class="col-span-2">
                        <label for="name"
                            class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Presentacion</label>
                        <input type="text" id="disabled-input" aria-label="disabled input"
                            class=" bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            value="{{ $articulo_seleccionado ? $articulo_seleccionado['descripcion'] : '' }}"
                            disabled>
                    </div>
                    {{-- PROVEEDOR --}}
                    <div class="col-span-1">
                        <label for="proveedor"
                            class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Proveedor</label>
                        <select id="proveedor" wire:model='id_proveedor'
                            class=" bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option selected value="">Seleccionar Proveedor</option>
                            @foreach ($this->proveedores as $index_prov => $prov)
                                <option wire:key='{{ $index_prov }}' value="{{ $prov->id }}">
                                    {{ $prov->nombre }}</option>
                            @endforeach
                        </select>
                        @error('id_proveedor')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                    {{-- Costo sin impuesto --}}
                    <div class="col-span-1">
                        <label for="costo"
                            class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Costo
                            S/Impuesto</label>
                        <input type="number" name="costo" id="costo"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Precio" wire:model='costo_unitario' wire:change='calcularPrecioIva'>
                        @error('costo_unitario')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                    {{-- IVA --}}
                    <div class="col-span-1">
                        <label for="costo"
                            class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">IVA</label>
                        <input type="number" name="costo" id="costo"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="0" wire:model='iva' wire:change='calcularPrecioIva'>
                    </div>
                    {{-- Costo con impuesto --}}
                    <div class="col-span-1">
                        <label for="costo"
                            class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Costo
                            C/Impuesto</label>
                        <input type="number" name="costo" id="costo"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="0" wire:model='costo_con_impuesto' wire:change='calcularPrecioSinIva'>
                    </div>
                </div>
                <!-- Modal footer -->
                <div
                    class="mt-2 flex items-center space-x-3 rtl:space-x-reverse border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button wire:click='agregarArticulo()' type="button"
                        class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Aceptar
                    </button>
                    <button wire:click='cancelar' type="button"
                        class="py-2.5 px-5 ms-3 text-sm font-medium text-red-900 focus:outline-none bg-red rounded-lg border border-red-200 hover:bg-red-100 hover:text-red-700 focus:z-10 focus:ring-4 focus:ring-red-100 dark:focus:ring-red-700 dark:bg-red-800 dark:text-red-400 dark:border-red-600 dark:hover:text-white dark:hover:bg-red-700">Cancelar
                    </button>
                </div>
            </div>
        </x-slot>
    </x-modal>
    {{-- Action message --}}
    <x-action-message on='compra'>
        @if (session('success-compra'))
            <div id="alert-exito"
                class="flex items-center p-4 mb-4 text-green-800 border-t-4 border-green-300 bg-green-50 dark:text-green-400 dark:bg-gray-800 dark:border-green-800"
                role="alert">
                <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                </svg>
                <div class="ms-3 text-sm font-medium">
                    {{ session('success-compra') }}
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
