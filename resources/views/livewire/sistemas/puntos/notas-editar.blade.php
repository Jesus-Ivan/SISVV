<div>
    {{-- TIPO DE CORRECCION --}}
    <div>
        <h6 class="text-lg font-bold dark:text-white">Datos de la correccion</h6>
        <hr class="h-1 my-2 bg-gray-200 border-0 dark:bg-gray-700">
        <div class="flex gap-4 items-end">
            {{-- Solicitante de la correccion --}}
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Solicitante</label>
                <select wire:model='solicitante_id'
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option selected value="{{ null }}">Seleccione</option>
                    @foreach ($users as $index => $user)
                        <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
                    @endforeach
                </select>
                @error('solicitante_id')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>
            {{-- Motivo de correccion --}}
            <div class="grow">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Motivo de correccion</label>
                <select wire:model='motivo_id'
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-fit p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option selected value="{{ null }}">Seleccione</option>
                    @foreach ($motivos_correccion as $index => $motivo)
                        <option value="{{ $motivo['id'] }}">{{ $motivo['descripcion'] }}</option>
                    @endforeach
                </select>
                @error('motivo_id')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>
            <div class="flex gap-2">
                {{-- Boton de cortesia --}}
                <button type="button" wire:click="$dispatch('open-modal', {name:'modalObservaciones'})"
                    class="h-11 focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Cortesia</button>
                {{-- Boton de eliminar venta completa --}}
                <button type="button" wire:click ="eliminarNota"
                    class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                    <svg class="w-6 h-6  aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="20"
                        height="20" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd"
                            d="M8.586 2.586A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h3a1 1 0 1 1 0 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8a1 1 0 0 1 0-2h3V4a2 2 0 0 1 .586-1.414ZM10 6h4V4h-4v2Zm1 4a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Zm4 0a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Z"
                            clip-rule="evenodd" />
                    </svg>
                    Eliminar nota
                </button>
            </div>
        </div>
    </div>
    {{-- DATOS DE LA VENTA --}}
    <div>
        <h6 class="text-lg font-bold dark:text-white">Datos de la venta</h6>
        <hr class="h-1 my-2 bg-gray-200 border-0 dark:bg-gray-700">
        <div class="flex items-end gap-4">
            {{-- Punto de venta --}}
            <div class="w-64">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Selecciona punto de
                    venta</label>
                <select wire:model='venta.clave_punto_venta'
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="{{ null }}">Escoger punto</option>
                    @foreach ($puntos as $index => $punto)
                        <option value="{{ $punto['clave'] }}">{{ $punto['nombre'] }}</option>
                    @endforeach
                </select>
            </div>
            {{-- Corte de caja --}}
            <input type="text" placeholder="Corte de caja" wire:model='venta.corte_caja'
                wire:keyup.ctrl="searchCajas"
                class="h-10 block max-w-32 p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <div>
                {{-- Total Original --}}
                <p>Total original: ${{ $venta['total'] }} MXN.</p>
                {{-- Fecha apertura --}}
                <p>Fecha apertura: {{ $venta['fecha_apertura'] }}</p>
                {{-- Fecha cierre --}}
                <p>Fecha cierre: {{ $venta['fecha_cierre'] }}</p>
            </div>
            <div>
                <span class="font-semibold flex gap-2">Tipo de venta: <p class="font-normal">{{ $venta['tipo_venta'] }}
                    </p></span>
            </div>
        </div>
    </div>
    {{-- DATOS DEL SOCIO --}}
    <div
        class="{{ $venta['tipo_venta'] == 'socio' || $venta['tipo_venta'] == 'invitado' ? '' : ' opacity-50 pointer-events-none' }}">
        <h6 class="text-lg font-bold dark:text-white">Datos del socio</h6>
        <hr class="h-1 my-2 bg-gray-200 border-0 dark:bg-gray-700">
        <div class="flex gap-2 items-end">
            {{-- TITULAR ACTUAL DE LA COMPRA --}}
            <div class="w-1/3">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Titular actual</label>
                <input type="text" aria-label="disabled input 2"
                    class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    value="{{ $venta['id_socio'] }} - {{ $venta['nombre'] }}" disabled readonly>
            </div>
            {{-- NUEVO TITULAR DE LA COMPRA --}}
            <div class="w-1/3">
                <livewire:autocomplete :params="[
                    'table' => ['name' => 'socios', 'columns' => ['id', 'nombre', 'apellido_p', 'apellido_m']],
                ]" primaryKey="id" event="on-selected-socio" />
            </div>
            <div class="flex w-1/3 items-center">
                <p class="w-full">Nuevo titular:
                    {{ $nuevo_socio ? $nuevo_socio['id'] . '-' . $nuevo_socio['nombre'] . ' ' . $nuevo_socio['apellido_p'] : '' }}
                </p>
                <button type="button" wire:click="limpiarTitular"
                    class="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2 text-center dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">Limpiar</button>
            </div>
        </div>
    </div>
    {{-- PRODUCTOS DE LA VENTA --}}
    <div>
        <h6 class="text-lg font-bold dark:text-white">Productos</h6>
        <hr class="h-1 my-2 bg-gray-200 border-0 dark:bg-gray-700">
        {{-- Tabla de edicion --}}
        <div class="max-h-96 relative overflow-y-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            CODIGO
                        </th>
                        <th scope="col" class="px-6 py-3">
                            DESCRIPCION
                        </th>
                        <th scope="col" class="px-6 py-3">
                            CANTIDAD
                        </th>
                        <th scope="col" class="px-6 py-3">
                            PRECIO UNITARIO
                        </th>
                        <th scope="col" class="px-6 py-3">
                            IMPORTE
                        </th>
                        <th scope="col" class="px-6 py-3">
                            ACCIONES
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productos as $index => $producto)
                        @if (!array_key_exists('deleted', $producto))
                            <tr wire:key='{{ $index }}'
                                class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                <th scope="row"
                                    class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $producto['codigo_catalogo'] }}
                                </th>
                                <td class="px-6 py-2">
                                    {{ $producto['nombre'] ?: $producto['catalogo_productos']['nombre'] }}
                                </td>
                                <td class="px-6 py-2">
                                    {{ $producto['cantidad'] }}
                                </td>
                                <td class="px-6 py-2">
                                    ${{ $producto['precio'] }}
                                </td>
                                <td class="px-6 py-2 flex items-center gap-2">
                                    $<input type="number" wire:model='productos.{{ $index }}.subtotal'
                                        wire:change='$refresh' step="0.01"
                                        class="block max-w-32 p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                </td>
                                <td class="px-6 py-2">
                                    <a wire:click='eliminarProducto({{ $index }})'
                                        class="font-medium text-red-600 dark:text-red-500 hover:underline">Eliminar</a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <th>Total: ${{ $this->total_productos }}</th>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    {{-- PAGOS DE LA VENTA  --}}
    <div>
        <h6 class="text-lg font-bold dark:text-white">Metodo de pago</h6>
        <hr class="h-1 my-2 bg-gray-200 border-0 dark:bg-gray-700">
        {{-- Tabla --}}
        <div class="max-h-96 relative overflow-y-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class=" px-6 py-3">
                            No.socio
                        </th>
                        <th scope="col" class=" px-6 py-3">
                            Nombre
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Metodo pago
                        </th>
                        <th scope="col" class="px-6 py-3">
                            IMPORTE
                        </th>
                        <th scope="col" class="px-6 py-3">
                            ACCIONES
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pagos as $index_pago => $pago)
                        @if (!array_key_exists('deleted', $pago))
                            <tr wire:key='{{ $index_pago }}'
                                class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                <th scope="row"
                                    class="{{ $venta['tipo_venta'] == 'socio' || $venta['tipo_venta'] == 'invitado' ? '' : ' opacity-50 pointer-events-none' }} px-6 py-1 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    <input type="number" wire:model='pagos.{{ $index_pago }}.id_socio'
                                        wire:keyup.enter='buscarSocioPago({{ $index_pago }})'
                                        class="block max-w-32 p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                </th>
                                <td class="px-6 py-1">
                                    <input type="text" wire:model='pagos.{{ $index_pago }}.nombre'
                                        class="{{ $venta['tipo_venta'] == 'socio' || $venta['tipo_venta'] == 'invitado' ? '' : ' opacity-50 pointer-events-none' }} block p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                </td>
                                <td class="px-6 py-1">
                                    <select wire:model='pagos.{{ $index_pago }}.id_tipo_pago'
                                        class="block p-2  text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        <option value="{{ null }}">Choose a country</option>
                                        @foreach ($tipos_pago as $pago)
                                            <option value="{{ $pago['id'] }}">{{ $pago['descripcion'] }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-6 py-1 flex items-center gap-2">
                                    $<input type="text" wire:model='pagos.{{ $index_pago }}.monto'
                                        wire:change='$refresh'
                                        class="block p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                </td>
                                <td class="px-6 py-1">
                                    <a wire:click='eliminarPago({{ $index_pago }})'
                                        class="font-medium text-red-600 dark:text-red-500 hover:underline">Eliminar</a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <th>Total: ${{ $this->total_pagos }}</th>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Botones de accion --}}
    <div class="w-full my-4">
        <button type="button" wire:click ='guardarCambios'
            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
            <div class="flex">
                Guardar cambios
            </div>
        </button>
    </div>
    {{-- Pantalla de carga --}}
    <div wire:loading wire:target='guardarCambios'>
        <x-loading-screen name='loading'>
            <x-slot name='body'>
                <div class="flex">
                    <div class="me-4">
                        @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
                    </div>
                    <p>Guardando cambios ...</p>
                </div>
            </x-slot>
        </x-loading-screen>
    </div>

    {{-- MODAL DE OBSERVACIONES --}}
    <x-modal title="Confirmar transformacion" name='modalObservaciones'>
        <x-slot name='body'>
            <p>Desea confirmar transformacion?</p>
            <p>Para la venta con folio: {{ $venta['folio'] }}</p>
            <input wire:model='observaciones' type="text" id="folio" placeholder="Observaciones"
                class="h-10 block w-48 p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            @error('observaciones')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror

            {{-- BOTON DE CONFIRMACION --}}
            <div>
                <button type="button" wire:click="confirmarCortesia"
                    class="h-11 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2  dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                    <!--Loading indicator-->
                    <div wire:loading wire:target='confirmarCortesia'>
                        @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
                    </div>
                    <div wire:loading.remove wire:target='confirmarCortesia'>
                        Convertir a cortesia
                    </div>
                </button>
            </div>
        </x-slot>
    </x-modal>

    {{-- Modal de cortes de caja --}}
    <x-modal title="Confirmar cortes de caja" name='modalCortes'>
        <x-slot name='body'>
            <div class="max-h-96 overflow-y-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                CORTE
                            </th>
                            <th scope="col" class="px-6 py-3">
                                USUARIO
                            </th>
                            <th scope="col" class="px-6 py-3">
                                FECHA APERTURA
                            </th>
                            <th scope="col" class="px-6 py-3">
                                FECHA CIERRE
                            </th>
                            <th scope="col" class="px-6 py-3">
                                PUNTO
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->cajas as $caja)
                            <tr wire:key='{{ $caja->corte }}'
                                class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $caja->corte }}
                                </th>
                                <td class="px-6 py-4">
                                    {{ $caja->users->name }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $caja->fecha_apertura }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $caja->fecha_cierre }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $caja->clave_punto_venta }}
                                </td>
                                <td class="px-6 py-4">
                                    <a wire:click='selectCaja({{ $caja->corte }})'
                                        class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Seleccionar</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </x-slot>
    </x-modal>
    {{-- Modal de motivo eliminacion --}}
    <x-modal name="modal-motivo eliminacion" title="Eliminar producto">
        <x-slot name='body'>
            <div>
                <p>{{ !is_null($producto_eliminar) ? $producto_eliminar['nombre'] : '' }}
                </p>
                <form class="max-w-sm mx-auto">
                    {{-- Select --}}
                    <div>
                        <label for="countries"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Motivo de
                            eliminacion</label>
                        <select id="countries" wire:model.live='id_eliminacion'
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option selected value="{{ null }}">Seleccione motivo</option>
                            @foreach ($this->conceptos as $i => $item)
                                <option value="{{ $item->id }}" wire:key='{{ $i }}'>
                                    {{ $item->descripcion }}</option>
                            @endforeach
                        </select>
                        @error('id_eliminacion')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                    {{-- Input text --}}
                    @if ($id_eliminacion ? $this->conceptos->find($id_eliminacion)->editable : false)
                        <input type="text" placeholder="Detalles ..." wire:model='motivo'
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        @error('motivo')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    @endif
                    {{-- Button --}}
                    <button type="button" wire:click='confirmarEliminacion()'
                        class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">Confirmar</button>
                </form>
            </div>
        </x-slot>
    </x-modal>

    {{-- ACTION MESSAGE --}}
    <x-action-message on='open-action-message'>
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
    </x-action-message>
</div>
