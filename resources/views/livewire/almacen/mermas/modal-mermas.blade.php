<div>
    <x-modal name="añadirMr" title="REGISTRAR MERMA">
        <x-slot:body>
            <form class="p-1 md:p-1">
                <!-- Modal content -->
                <div class="h-auto max-w-4xl overflow-y-auto">
                    <div class="grid gap-2 grid-cols-3">
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
                            @error('articulo')
                                <x-input-error messages="{{ $message }}" />
                            @enderror
                        </div>
                        {{-- CODIGO --}}
                        <div class="col-span-1">
                            <label for="codigo"
                                class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Código</label>
                            <input type="text" id="disabled-input" aria-label="disabled input"
                                class=" bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                value="{{ $articulo['codigo'] }}" disabled>
                        </div>
                        {{-- NOMBRE DEL ARTICULO --}}
                        <div class="col-span-2">
                            <label for="name"
                                class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Nombre del
                                articulo</label>
                            <input type="text" id="disabled-input" aria-label="disabled input"
                                class=" bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                value="{{ $articulo['nombre'] }}" disabled>
                        </div>
                        {{-- TIPO MERMA --}}
                        <div class="col-span-1">
                            <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Tipo de
                                Merma</label>
                            <select wire:model='tipo_merma'
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option selected value="{{ null }}">SELECCIONAR</option>
                                @foreach ($this->tipoMerma as $index => $item)
                                    <optgroup wire:key='{{ $index }}' label="{{ $index }}">
                                        @foreach ($item as $tipo)
                                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            @error('tipo_merma')
                                <x-input-error messages="{{ $message }}" />
                            @enderror
                        </div>
                        {{-- ORIGEN MERMA --}}
                        <div class="col-span-1">
                            <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Origen de
                                Merma</label>
                            <select wire:model='origen_merma' wire:change='changedOrigen($event.target.value)'
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option selected value="{{ null }}">SELECCIONAR</option>
                                @foreach ($this->bodegas as $index => $bodega)
                                    <option value="{{ $bodega->clave }}">{{ $bodega->descripcion }}</option>
                                @endforeach
                            </select>
                            @error('origen_merma')
                                <x-input-error messages="{{ $message }}" />
                            @enderror
                        </div>
                        {{-- Stock --}}
                        <div class="col-span-1 sm:col-span-1 mt-4">
                            <div wire:loading.remove wire:target='origen_merma'>
                                <div class="flex gap-2">
                                    <p class="font-semibold text-sm">Stock unitario:</p>
                                    <p>{{ $origen_merma && $stock_unitario ? $stock_unitario[$origen_merma] : '' }}</p>
                                </div>
                                <div class="flex gap-2">
                                    <p class="font-semibold text-sm">Stock peso:</p>
                                    <p>{{ $origen_merma && $stock_peso ? $stock_peso[$origen_merma] : '' }}</p>
                                </div>
                            </div>
                        </div>
                        {{-- Cantidad --}}
                        <div class="col-span-1 sm:col-span-1">
                            <label for="st_min"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cantidad</label>
                            <input type="number" name="st_min" wire:model='cantidad'
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            @error('cantidad')
                                <div class="text-red-500 text-sm">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- Unidad --}}
                        <div class="col-span-1 sm:col-span-1">
                            <label for="clave_dpto"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Unidad</label>
                            <select id="clave_dpto" wire:model='id_unidad'
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option selected value="{{ null }}">SELECCIONAR</option>
                                @foreach ($this->unidades as $index => $unidad)
                                    <option value="{{ $unidad->id }}">{{ $unidad->descripcion }}</option>
                                @endforeach
                            </select>
                            @error('id_unidad')
                                <div class="text-red-500 text-sm">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="flex items-end col-span-1 sm:col-span-1">
                            <div class="flex items-center m-2">
                                <input id="checked-checkbox" type="checkbox" wire:model='descontar'
                                    class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="checked-checkbox"
                                    class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Descontar
                                    stock</label>
                            </div>
                        </div>
                        <div class="col-span-3">
                            <label
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Observaciones</label>
                            <input type="text" wire:model='observaciones'
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Observaciones de la merma" required />
                        </div>
                    </div>
                </div>
            </form>
        </x-slot:body>
        <x-slot:footer>
            <button wire:click='registrarMerma' type="button"
                class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                <!--Loading indicator-->
                <div wire:loading.delay wire:target='registrarMerma' class="me-4">
                    @include('livewire.utils.loading', ['w' => 4, 'h' => 4])
                </div>
                Aceptar
            </button>
            <button type="button"
                class="py-2.5 px-5 ms-3 text-sm font-medium text-red-900 focus:outline-none bg-red rounded-lg border border-red-200 hover:bg-red-100 hover:text-red-700 focus:z-10 focus:ring-4 focus:ring-red-100 dark:focus:ring-red-700 dark:bg-red-800 dark:text-red-400 dark:border-red-600 dark:hover:text-white dark:hover:bg-red-700">Cancelar
            </button>
        </x-slot:footer>
    </x-modal>
</div>
