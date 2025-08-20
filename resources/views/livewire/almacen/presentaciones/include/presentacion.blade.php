<div>
    <div class="grid grid-cols-2 gap-4 px-4">
        {{-- Primera columna --}}
        <div>
            {{-- Codigo y descripcion --}}
            <div class="flex gap-2 w-full">
                <div class="w-32">
                    <label for="clave"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Clave</label>
                    <input type="text" id="clave" wire:model='form.clave' aria-label="disabled input" disabled
                        class="cursor-not-allowed bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="#" />
                </div>
                <div class="w-full">
                    <label for="descripcion"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Descripcion</label>
                    <input type="text" id="descripcion" wire:model='form.descripcion'
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Nombre presentacion" />
                    @error('form.descripcion')
                        <x-input-error messages="{{ $message }}" />
                    @enderror
                </div>
            </div>
            {{-- Select Grupo --}}
            <div>
                <label for="grupo" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Grupo</label>
                <select id="grupo" wire:model='form.id_grupo'
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="{{ null }}">Seleccione</option>
                    @foreach ($this->grupos as $index_g => $grupo)
                        <option wire:key="{{ $index_g }}" value="{{ $grupo->id }}">{{ $grupo->descripcion }}
                        </option>
                    @endforeach
                </select>
                @error('form.id_grupo')
                    <x-input-error messages="{{ $message }}" />
                @enderror
            </div>
            {{-- Select Proveedor --}}
            <div>
                <label for="proveedor"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Proveedor</label>
                <select id="proveedor" wire:model='form.id_proveedor'
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="{{ null }}">Seleccione</option>
                    @foreach ($this->proveedores as $index => $item)
                        <option wire:key={{ $index }} value="{{ $item->id }}">{{ $item->nombre }}</option>
                    @endforeach
                </select>
                @error('form.id_proveedor')
                    <x-input-error messages="{{ $message }}" />
                @enderror
            </div>
            {{-- Input Ultimo costo --}}
            <div class="w-full">
                <label for="u_costo" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ultimo
                    costo</label>
                <input type="number" id="u_costo" step="0.01" wire:model='form.ultimo_costo'
                    wire:change='changedCosto'
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="$00.0" />
            </div>
            {{-- Input iva y Costo con impuesto --}}
            <div class="flex gap-2 w-full">
                <div class="w-full">
                    <label for="iva"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Iva</label>
                    <input type="number" id="iva" step="0.01" wire:model='form.iva' wire:change='changedIva'
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="16%" />
                </div>
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
            </div>
            {{-- Select Estado --}}
            <div class="flex gap-2 w-full">
                <div class="w-full">
                    <label for="Estado"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Estado</label>
                    <select id="Estado" wire:model='form.estado'
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option selected value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                    @error('form.estado')
                        <x-input-error messages="{{ $message }}" />
                    @enderror
                </div>
                <div class="w-full">
                    <label for="u_compra" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ultima
                        compra</label>
                    <input type="date" id="u_compra" wire:model='form.ultima_compra' @disabled($this->form->original)
                        class="{{ $this->form->original ? 'cursor-not-allowed' : '' }} bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                </div>
            </div>
        </div>
        {{-- Segunda columna --}}
        <div>
            <div class="py-7">
                {{-- Search bar Insumo base --}}
                <livewire:search-bar tittle="Insumo Base" table="insumos" :columns="['clave', 'descripcion']" primary="clave"
                    event="selected-insumo" />
                {{-- Insumo base Descripcion --}}
                <div class="flex gap-2 items-end">
                    <div class="w-full">
                        <label for="insumo_base"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Insumo
                            Base</label>
                        <input type="text" id="insumo_base" aria-label="disabled input"
                            value="{{ $this->form->insumo_base ? $this->form->insumo_base->clave . ' ' . $this->form->insumo_base->descripcion : '' }}"
                            disabled
                            class="cursor-not-allowed bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="#" />
                        @error('form.insumo_base')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                    <button type="button" wire:click='limpiarInsumoBase'
                        class="h-11 text-gray-700 border border-gray-700 hover:bg-gray-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center dark:border-gray-500 dark:text-gray-500 dark:hover:text-white dark:focus:ring-gray-800 dark:hover:bg-gray-500">
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M14.502 7.046h-2.5v-.928a2.122 2.122 0 0 0-1.199-1.954 1.827 1.827 0 0 0-1.984.311L3.71 8.965a2.2 2.2 0 0 0 0 3.24L8.82 16.7a1.829 1.829 0 0 0 1.985.31 2.121 2.121 0 0 0 1.199-1.959v-.928h1a2.025 2.025 0 0 1 1.999 2.047V19a1 1 0 0 0 1.275.961 6.59 6.59 0 0 0 4.662-7.22 6.593 6.593 0 0 0-6.437-5.695Z" />
                        </svg>
                    </button>
                </div>
                {{-- Rendimiento presentacion y Unidad --}}
                <div class="flex gap-2 w-full">
                    <div class="w-full">
                        <label for="rendi"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Redimiento
                            insumo</label>
                        <input type="number" id="rendi" wire:model='form.rendimiento'
                            wire:change='calcularEquivalencias'
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="50" />
                        @error('form.rendimiento')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                    <div class="w-full">
                        <label for="uni"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Unidad insumo</label>
                        <input type="text" id="uni" aria-label="disabled input" disabled
                            value="{{ $this->form->unidad_insumo ? $this->form->unidad_insumo->descripcion : '' }}"
                            class="cursor-not-allowed bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="#" />
                    </div>
                </div>
                {{-- Separador --}}
                <hr class="w-full h-1 my-8 bg-gray-200 border-0 dark:bg-gray-700">
                {{-- Equivalencias --}}
                <div class="flex gap-3">
                    <div class="w-full">
                        <label
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Costo/Rendimiento</label>
                        <input type="number" wire:model='form.c_rendimiento' aria-label="disabled input" disabled
                            class="cursor-not-allowed bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="0" step="0.001" />
                    </div>
                    <div class="w-full">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Costo/Rendimiento
                            Impuesto</label>
                        <input type="number" wire:model='form.c_rendimiento_imp' aria-label="disabled input"
                            disabled
                            class="cursor-not-allowed bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="0" step="0.001" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Botones de accion --}}
    <div class="px-4 py-2">
        {{-- Boton de regresar --}}
        <a type="button" href="{{ route('almacen.presentaciones') }}"
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
            Guardar Presentacion
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
                    <p>Procesando presentacion</p>
                </div>
            </x-slot>
        </x-loading-screen>
    </div>
</div>
