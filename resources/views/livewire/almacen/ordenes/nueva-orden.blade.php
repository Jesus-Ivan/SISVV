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
                <h4 class="flex items-center ms-2 text-2xl font-bold dark:text-white">NUEVA ORDEN DE COMPRA</h4>
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

    <div class="container py-2">
        <div class="flex ms-3">
            <div class="inline-flex flex-grow gap-3 items-end">
                {{-- TIPO DE ORDEN --}}
                <div>
                    <label for="departamento"
                        class="flex items-center mb-1 text-lg font-medium text-gray-900 dark:text-white">Tipo de
                        orden:</label>
                    <select id="departamento" wire:model='tipo_orden'
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 w-fit p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                        <option selected value="{{ null }}">Seleccionar Tipo</option>
                        <option value="G">General</option>
                        <option value="E">Evento</option>
                    </select>
                    @error('tipo_orden')
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
                            <th scope="col" class="px-2 py-3 min-w-32">
                                DESCRIPCIÓN
                            </th>
                            <th scope="col" class="px-2 py-3">
                                UNIDAD
                            </th>
                            <th scope="col" class="px-2 py-3">
                                PROVEEDOR
                            </th>
                            <th scope="col" class="px-2 py-3">
                                CANTIDAD
                            </th>
                            <th scope="col" class="px-2 py-3">
                                COSTO
                            </th>
                            <th scope="col" class="px-2 py-3">
                                IMPORTE
                            </th>
                            <th scope="col" class="px-2 py-3">
                                IVA
                            </th>
                            <th scope="col" class="px-2 py-3">
                                ALMACÉN
                            </th>
                            <th scope="col" class="px-2 py-3">
                                BAR
                            </th>
                            <th scope="col" class="px-2 py-3">
                                BARRA
                            </th>
                            <th scope="col" class="px-2 py-3">
                                CADDIE
                            </th>
                            <th scope="col" class="px-2 py-3">
                                CAFETERÍA
                            </th>
                            <th scope="col" class="px-2 py-3">
                                COCINA
                            </th>
                            <th scope="col" class="px-2 py-3 min-w-32">
                                ULTIMA COMPRA
                            </th>
                            <th scope="col" class="px-2 py-3">
                                ACCIONES
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lista_articulos as $index => $articulo)
                            <tr wire:key='{{ $index }}'
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-2 py-2">
                                    {{ $articulo['nombre'] }}
                                </td>
                                <td class="px-2 py-2">
                                    {{ $this->unidades->find($articulo['id_unidad'])->descripcion }}
                                </td>
                                <td class="px-2 py-2">
                                    {{ $this->proveedores->find($articulo['id_proveedor'])->nombre }}
                                </td>
                                <td class="px-2 py-2">
                                    @if ($index_articulo == $index)
                                        <input type="number" id="small-input-{{ $index }}"
                                            wire:model="articulo_editando.cantidad"
                                            class="block w-16 p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    @else
                                        {{ $articulo['cantidad'] }}
                                    @endif
                                </td>
                                <td class="px-2 py-2">
                                    @if ($index_articulo == $index)
                                        <input type="number" id="small-input-{{ $index }}"
                                            wire:model="articulo_editando.costo_unitario"
                                            class="block w-16 p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    @else
                                        ${{ $articulo['costo_unitario'] }}
                                    @endif
                                </td>
                                <td class="px-2 py-2">
                                    ${{ $articulo['importe'] }}
                                </td>
                                <td class="px-2 py-2">
                                    @if ($index_articulo == $index)
                                        <input type="number" id="small-input-{{ $index }}"
                                            wire:model="articulo_editando.iva_cant"
                                            class="block w-16 p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    @else
                                        ${{ $articulo['iva_cant'] }}
                                    @endif
                                </td>
                                <td class="px-2 py-2">
                                    @foreach ($articulo['almacen'] as $tipo => $item)
                                        <p class=" uppercase">{{ substr($tipo, 0, 1) }} : {{ $item }}</p>
                                    @endforeach
                                </td>
                                <td class="px-2 py-2">
                                    @foreach ($articulo['bar'] as $tipo => $item)
                                        <p class=" uppercase">{{ substr($tipo, 0, 1) }} : {{ $item }}</p>
                                    @endforeach
                                </td>
                                <td class="px-2 py-2">
                                    @foreach ($articulo['barra'] as $tipo => $item)
                                        <p class=" uppercase">{{ substr($tipo, 0, 1) }} : {{ $item }}</p>
                                    @endforeach
                                </td>
                                <td class="px-2 py-2">
                                    @foreach ($articulo['caddie'] as $tipo => $item)
                                        <p class=" uppercase">{{ substr($tipo, 0, 1) }} : {{ $item }}</p>
                                    @endforeach
                                </td>
                                <td class="px-2 py-2">
                                    @foreach ($articulo['cafeteria'] as $tipo => $item)
                                        <p class=" uppercase">{{ substr($tipo, 0, 1) }} : {{ $item }}</p>
                                    @endforeach
                                </td>
                                <td class="px-2 py-2">
                                    @foreach ($articulo['cocina'] as $tipo => $item)
                                        <p class=" uppercase">{{ substr($tipo, 0, 1) }} : {{ $item }}</p>
                                    @endforeach
                                </td>
                                <td class="px-2 py-2 min-w-32">
                                    {{ $articulo['ultima_compra'] }}
                                </td>
                                <td class="px-2 py-2">
                                    @if ($index_articulo == $index)
                                        <div class="flex">
                                            <button type="button" wire:click='confirmarEdicion({{ $index }})'
                                                class="text-gr-700 border border-gray-700 hover:bg-gray-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-gray-500 dark:text-gray-500 dark:hover:text-white dark:focus:ring-gray-800 dark:hover:bg-gray-500">
                                                <svg class="w-5 h-5 " aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    fill="currentColor" viewBox="0 0 24 24">
                                                    <path fill-rule="evenodd"
                                                        d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                <span class="sr-only">Listo</span>
                                            </button>
                                            <button type="button" wire:click='cancelarEdicion'
                                                class="text-gray-700 border border-gray-700 hover:bg-gray-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-gray-500 dark:text-gray-500 dark:hover:text-white dark:focus:ring-gray-800 dark:hover:bg-gray-500">
                                                <svg class="w-5 h-5" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    fill="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        d="M14.502 7.046h-2.5v-.928a2.122 2.122 0 0 0-1.199-1.954 1.827 1.827 0 0 0-1.984.311L3.71 8.965a2.2 2.2 0 0 0 0 3.24L8.82 16.7a1.829 1.829 0 0 0 1.985.31 2.121 2.121 0 0 0 1.199-1.959v-.928h1a2.025 2.025 0 0 1 1.999 2.047V19a1 1 0 0 0 1.275.961 6.59 6.59 0 0 0 4.662-7.22 6.593 6.593 0 0 0-6.437-5.695Z" />
                                                </svg>
                                                <span class="sr-only">Cancelar</span>
                                            </button>
                                        </div>
                                    @else
                                        <div class="flex">
                                            <button type="button" wire:click ='editarArticulo({{ $index }})'
                                                class="text-green-700 hover:text-white border border-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-3.5 py-1.5 text-center me-2 mb-2 dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-600 dark:focus:ring-green-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                    fill="currentColor" class="w-5 h-5">
                                                    <path
                                                        d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z" />
                                                    <path
                                                        d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z" />
                                                </svg>

                                            </button>
                                            <button type="button"
                                                wire:click ="eliminarArticulo({{ $index }})"
                                                class="text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-3.5 py-1.5 text-center me-2 mb-2 dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                    fill="currentColor" class="w-5 h-5">
                                                    <path fill-rule="evenodd"
                                                        d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    @endif

                                </td>
                            </tr>
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
    <x-modal name="modal-articulos" title="AÑADIR ARTICULO A LA LISTA">
        <x-slot name='body'>
            <!-- Modal content -->
            <div class="h-auto max-w-4xl overflow-y-auto">
                <!-- Modal body -->
                <div class="grid gap-2 grid-cols-3">
                    {{-- BARRA BUSQUEDA ARTICULO --}}
                    <div class="col-span-3">
                        <livewire:search-input :params="[
                            'tittle_bar' => 'Codigo o nombre articulo',
                            'table_name' => 'catalogo_vista_verde',
                            'table_columns' => ['codigo', 'nombre'],
                            'primary_key' => 'codigo',
                            'event' => 'selected-articulo',
                            'dpto' => ['ALM', 'PV'],
                            'tipo' => null,
                        ]" />
                    </div>
                    {{-- CODIGO --}}
                    <div class="col-span-1">
                        <label for="codigo"
                            class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Código</label>
                        <input type="text" id="disabled-input" aria-label="disabled input"
                            class=" bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            value="{{ $articulo_seleccionado ? $articulo_seleccionado['codigo'] : '' }}" disabled>
                    </div>
                    {{-- NOMBRE DEL ARTICULO --}}
                    <div class="col-span-2">
                        <label for="name"
                            class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Nombre del
                            producto</label>
                        <input type="text" id="disabled-input" aria-label="disabled input"
                            class=" bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            value="{{ $articulo_seleccionado ? $articulo_seleccionado['nombre'] : '' }}" disabled>
                    </div>
                    {{-- UNIDAD DE MEDIDA --}}
                    <div class="col-span-1">
                        <label for="unidad"
                            class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Unidad de
                            medida</label>
                        <select id="unidad" wire:model='id_unidad' wire:change='changeUnidad($event.target.value)'
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option selected value="{{ null }}">Seleccionar Unidad</option>
                            @foreach ($this->unidadesArticulo as $index => $item)
                                <option wire:key='{{ $index }}' value="{{ $item->id_unidad }}">
                                    {{ $item->unidad->descripcion }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_unidad')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
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
                    {{-- FECHA ULTIMA COMPRA --}}
                    <div class="col-span-1">
                        <label for="fecha"
                            class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Fecha de
                            ultima compra</label>
                        <input type="text" id="disabled-input" aria-label="disabled input"
                            value="{{ $articulo_seleccionado ? $articulo_seleccionado['ultima_compra'] : '' }}"
                            class="mb-4 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            value="Fecha" disabled>
                    </div>
                    {{-- Tabla de existencias --}}
                    <div class="col-span-3">
                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-2 text-center">

                                        </th>
                                        <th scope="col" class="px-6 py-2 text-center">
                                            ALMACÉN
                                        </th>
                                        <th scope="col" class="px-6 py-2 text-center">
                                            BAR
                                        </th>
                                        <th scope="col" class="px-6 py-2 text-center">
                                            BARRA
                                        </th>
                                        <th scope="col" class="px-6 py-2 text-center">
                                            CADDIE BAR
                                        </th>
                                        <th scope="col" class="px-6 py-2 text-center">
                                            CAFETERÍA
                                        </th>
                                        <th scope="col" class="px-6 py-2 text-center">
                                            COCINA
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stock as $index => $row)
                                        <tr wire:key='{{ $index }}'
                                            class="uppercase bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                            <th scope="row" class="px-6 py-2 text-center">
                                                {{ $row['tipo'] }}
                                            </th>
                                            <td class="px-6 py-2 text-center">
                                                {{ $row['stock_alm'] }}
                                            </td>
                                            <td class="px-6 py-2 text-center">
                                                {{ $row['stock_bar'] }}
                                            </td>
                                            <td class="px-6 py-2 text-center">
                                                {{ $row['stock_res'] }}
                                            </td>
                                            <td class="px-6 py-2 text-center">
                                                {{ $row['stock_cad'] }}
                                            </td>
                                            <td class="px-6 py-2 text-center">
                                                {{ $row['stock_caf'] }}
                                            </td>
                                            <td class="px-6 py-2 text-center">
                                                {{ $row['stock_coc'] }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @error('stock')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                    {{-- CANTIDAD --}}
                    <div class="col-span-1">
                        <label for="cantidad"
                            class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Cantidad</label>
                        <input type="number" name="cantidad" id="cantidad"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Cantidad" wire:model='cantidad'>
                        @error('cantidad')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                    {{-- COSTO UNITARIO --}}
                    <div class="col-span-1">
                        <label for="costo"
                            class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Costo
                            Unitario</label>
                        <input type="number" name="costo" id="costo"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Precio" wire:model='costo_unitario'>
                        @error('costo_unitario')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                    {{-- APLICA IVA? --}}
                    <div class="col-span-1">
                        <div class="flex gap-2">
                            {{-- Select --}}
                            <div>
                                <label for="unidad"
                                    class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">¿Aplica
                                    IVA?</label>
                                <select id="iva" wire:model.live='iva'
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                    <option value="{{ false }}" selected>No</option>
                                    <option value="{{ true }}">Si</option>
                                </select>
                            </div>
                            {{-- Input % --}}
                            <div class="{{ $iva ? '' : 'opacity-50 pointer-events-none' }}">
                                <label for="cantidad"
                                    class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">%</label>
                                <input type="number" name="cantidad" id="cantidad" wire:model='iva_cant'
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="Cantidad" required="">
                            </div>
                        </div>
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
