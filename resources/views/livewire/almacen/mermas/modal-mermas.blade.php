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
                        </div>
                        {{-- CODIGO --}}
                        <div class="col-span-1">
                            <label for="codigo"
                                class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Código</label>
                            <input type="text" id="disabled-input" aria-label="disabled input"
                                class=" bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                value="" disabled>
                        </div>
                        {{-- NOMBRE DEL ARTICULO --}}
                        <div class="col-span-2">
                            <label for="name"
                                class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Nombre del
                                producto</label>
                            <input type="text" id="disabled-input" aria-label="disabled input"
                                class=" bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                value="" disabled>
                        </div>
                        {{-- ORIGEN MERMA --}}
                        <div class="col-span-1">
                            <label for="clave_dpto"
                                class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Tipo de
                                Merma</label>
                            <select id="clave_dpto" wire:model=''
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option selected value="{{ null }}">SELECCIONAR</option>

                            </select>
                            @error('clave_dpto')
                                <x-input-error messages="{{ $message }}" />
                            @enderror
                        </div>
                        {{-- ORIGEN MERMA --}}
                        <div class="col-span-1">
                            <label for="clave_dpto"
                                class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Origen de
                                Merma</label>
                            <select id="clave_dpto" wire:model=''
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option selected value="{{ null }}">SELECCIONAR</option>

                            </select>
                            @error('clave_dpto')
                                <x-input-error messages="{{ $message }}" />
                            @enderror
                        </div>
                        {{-- Stock --}}
                        <div class="col-span-1 sm:col-span-1 mt-4">
                            <div wire:loading.remove wire:target='origen_seleccionado'>
                                <div class="flex gap-2">
                                    <p class="font-semibold text-sm">Stock unitario:</p>
                                    <p></p>
                                </div>
                                <div class="flex gap-2">
                                    <p class="font-semibold text-sm">Stock peso:</p>
                                    <p></p>
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
                        <div class="col-span-3">
                            <label for="number-input"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Motivo</label>
                            <input type="text" id=""
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Motivo de la merma" required />
                        </div>
                    </div>
                </div>
            </form>
        </x-slot:body>
        <x-slot:footer>
            <button wire:click='' type="button"
                class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Aceptar
            </button>
            <button wire:click='cancelar' type="button"
                class="py-2.5 px-5 ms-3 text-sm font-medium text-red-900 focus:outline-none bg-red rounded-lg border border-red-200 hover:bg-red-100 hover:text-red-700 focus:z-10 focus:ring-4 focus:ring-red-100 dark:focus:ring-red-700 dark:bg-red-800 dark:text-red-400 dark:border-red-600 dark:hover:text-white dark:hover:bg-red-700">Cancelar
            </button>
        </x-slot:footer>
    </x-modal>
</div>
