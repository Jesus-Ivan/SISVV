<div>
    {{-- Datos generales Insumo --}}
    <div class="grid grid-cols-3 gap-2 ">
        {{-- Input descripcion --}}
        <div class="w-full">
            <label for="Descripcion"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Descripcion</label>
            <input type="text" id="Descripcion" wire:model='form.descripcion'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="placheholder" />
            @error('form.descripcion')
                <x-input-error messages="{{ $message }}" />
            @enderror
        </div>
        {{-- Select grupo --}}
        <div>
            <label for="grupo" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Grupo
                insumos</label>
            <select id="grupo" wire:model='form.id_grupo'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="{{ null }}">Seleccione</option>
                @foreach ($this->grupos as $item)
                    <option wire:key='{{ $item->id }}' value="{{ $item->id }}">{{ $item->descripcion }}</option>
                @endforeach
            </select>
            @error('form.id_grupo')
                <x-input-error messages="{{ $message }}" />
            @enderror
        </div>
        {{-- Select Unidad --}}
        <div>
            <div>
                <label for="unidad" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Unidad
                    medida</label>
                <select id="unidad" wire:model='form.id_unidad' @disabled($this->form->original)
                    class="{{ $this->form->original ? 'cursor-not-allowed' : '' }} bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="{{ null }}">Seleccione</option>
                    @foreach ($this->unidades as $item)
                        <option wire:key='{{ $item->id }}' value="{{ $item->id }}">{{ $item->descripcion }}
                        </option>
                    @endforeach
                </select>
                @error('form.id_unidad')
                    <x-input-error messages="{{ $message }}" />
                @enderror
            </div>
        </div>
        {{-- Ultimo costo --}}
        <div class="w-full">
            <label for="u_costo" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ultimo
                costo</label>
            <input type="number" id="u_costo" step="0.01" wire:model='form.costo' wire:change='changedCosto'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="$00.0" />
            @error('form.costo')
                <x-input-error messages="{{ $message }}" />
            @enderror
        </div>
        {{-- IVA --}}
        <div>
            <div class="w-full">
                <label for="iva" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Iva</label>
                <input type="number" id="iva" step="0.01" wire:model='form.iva' wire:change='changedIva'
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="16%" />
            </div>
        </div>
        {{-- COSTO CON IMPUESTO --}}
        <div class="w-full">
            <label for="c_c_impuesto" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Costo
                con inpuesto</label>
            <input type="number" id="c_c_impuesto" step="0.01" wire:model='form.costo_iva'
                wire:change='changedCostoIva'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="$0.00" />
            @error('form.costo_iva')
                <x-input-error messages="{{ $message }}" />
            @enderror
        </div>
        {{-- ULTIMA COMPRA --}}
        <div class="w-full">
            <label for="u_compra" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ultima
                compra</label>
            <input type="date" id="u_compra" step="0.01" wire:model='form.ultima_compra'
                @disabled($this->form->original)
                class="{{ $this->form->original ? 'cursor-not-allowed' : '' }} bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="$0.00" />
        </div>
        {{-- CHECK INVENTARIABLE --}}
        <div class="flex flex-col items-center justify-end">
            <div class="flex">
                <div class="flex items-center h-5">
                    <input wire:model='form.inventariable' id="helper-checkbox" aria-describedby="helper-checkbox-text"
                        type="checkbox"
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                </div>
                <div class="ms-2 text-sm">
                    <label for="helper-checkbox" class="font-medium text-gray-900 dark:text-gray-300">Es
                        inventariable</label>
                    <p id="helper-checkbox-text" class="text-xs font-normal text-gray-500 dark:text-gray-300">Aparece en
                        el reporte de existencias y ajuste de inventarios</p>
                </div>
            </div>
        </div>
    </div>
    {{-- Insumo elaborado separador --}}
    <div class="{{ $this->form->original ? 'pointer-events-none opacity-50' : '' }} flex items-center my-3 ">
        <div class="flex w-60">
            <div class="flex items-center h-5">
                <input wire:model.live='form.elaborado' id="es-inv" aria-describedby="es-inv-text" type="checkbox"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
            </div>
            <div class="ms-2 text-sm">
                <label for="es-inv" class="w-full font-medium text-gray-900 dark:text-gray-300">Insumo
                    elaborado</label>
                <p id="es-inv-text" class="text-xs font-normal text-gray-500 dark:text-gray-300">Necesita de otros
                    insumos</p>
            </div>
        </div>
        <!--Linea -->
        <hr class="h-px my-2 w-full bg-gray-300 border-0 dark:bg-gray-700">
    </div>
    {{-- Search bar y Rendimiento elaborado --}}
    <div class="{{ $this->form->elaborado ? '' : 'pointer-events-none opacity-50' }}">
        {{-- Search bar y Rendimiento input --}}
        <div class="flex items-end gap-4">
            {{-- Search bar Insumo --}}
            <div class="flex-1">
                <livewire:search-bar :params="[
                    'tittle_bar' => 'Insumo de receta',
                    'table_name' => 'insumos',
                    'table_columns' => ['clave', 'descripcion'],
                    'primary_key' => 'clave',
                    'event' => 'selected-insumo',
                ]" />
            </div>
            {{-- Rendimiento --}}
            <div class="w-64">
                <label for="rendimiento"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Rendimiento receta</label>
                <input type="number" step="0.001" id="rendimiento" wire:model='form.rendimiento'
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="placheholder" />
                @error('form.rendimiento')
                    <x-input-error messages="{{ $message }}" />
                @enderror
            </div>
        </div>
    </div>
    {{-- Tabla insumo elaborado --}}
    <div class="my-2 {{ $this->form->elaborado ? '' : 'pointer-events-none opacity-50' }}">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            #
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Insumo
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Cantidad
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Unidad
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Costo c.impuesto
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Total
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($this->form->subtable as $index => $item)
                        @if (!array_key_exists('deleted', $item))
                            <tr wire:key ='{{ $index }}'
                                class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                <th scope="row"
                                    class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $item['clave'] }}
                                </th>
                                <td class="px-6 py-2">
                                    {{ $item['descripcion'] }}
                                </td>
                                <td class="px-6 py-2">
                                    <input type="number" step="0.001"
                                        wire:model='form.subtable.{{ $index }}.cantidad'
                                        wire:change ='changedCantidad'
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="0.000" />
                                </td>
                                <td class="px-6 py-2">
                                    {{ $item['unidad']['descripcion'] }}
                                </td>
                                <td class="px-6 py-2">
                                    ${{ $item['costo_con_impuesto'] }}
                                </td>
                                <td class="px-6 py-2">
                                    ${{ number_format($item['total'], 2) }}
                                </td>
                                <td class="px-6 py-2">
                                    <button type="button" wire:click="eliminarInsumo({{ $index }})"
                                        class="text-red-700 border border-red-700 hover:bg-red-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center  dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:focus:ring-red-800 dark:hover:bg-red-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                            fill="currentColor" class="w-5 h-5">
                                            <path fill-rule="evenodd"
                                                d="M8.586 2.586A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h3a1 1 0 1 1 0 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8a1 1 0 0 1 0-2h3V4a2 2 0 0 1 .586-1.414ZM10 6h4V4h-4v2Zm1 4a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Zm4 0a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span class="sr-only">Borrar</span>
                                    </button>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        @error('form.subtable')
            <x-input-error messages="{{ $message }}" />
        @enderror
    </div>

    {{-- Botones de accion --}}
    <div>
        {{-- Boton de regresar --}}
        <a type="button" href="{{ route('almacen.insumos') }}"
            class="my-2 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
            <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 12h14M5 12l4-4m-4 4 4 4" />
            </svg>Regresar
        </a>
        {{-- Boton de guardar cambios --}}
        <a type="button" wire:click='guardar'
            class="my-2 text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
            <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                height="24" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd"
                    d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7.414A2 2 0 0 0 20.414 6L18 3.586A2 2 0 0 0 16.586 3H5Zm10 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM8 7V5h8v2a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1Z"
                    clip-rule="evenodd" />
            </svg>
            Guardar Insumo
        </a>
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
                    <p>Procesando Insumo ... </p>
                </div>
            </x-slot>
        </x-loading-screen>
    </div>
</div>
