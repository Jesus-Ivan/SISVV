<div>
    <form class="p-1 md:p-1">
        <div class="grid gap-1 mb-2 grid-cols-2">
            {{-- Barra de busqueda --}}
            <div class="col-span-2">
                <label for="name" class=" text-sm font-medium text-gray-900 dark:text-white">Buscar articulo</label>
                <div class="flex">
                    <select id="ingredientes" wire:model.live="selectedIngrediente"
                        class="me-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-fit p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option selected>Seleccionar</option>
                        <option value="materia">Materia Prima</option>
                        <option value="insumo">Insumo</option>
                    </select>
                    {{-- Dependiendo que opcion seleccione, aparecera la barra de busqueda con la tabla --}}
                    @if ($selectedIngrediente === 'materia')
                        <livewire:autocomplete :params="['table_name' => 'ipa_inventario_principal', 'columns' => ['nombre', 'codigo']]" event="on-selected-materia" primary='codigo' />
                    @elseif ($selectedIngrediente === 'insumo')
                        <livewire:autocomplete :params="['table_name' => 'ico_insumos', 'columns' => ['nombre', 'codigo']]" event="on-selected-insumo" primary='codigo' />
                    @endif
                </div>
            </div>
            <div class="col-span-2">
                <label for="name"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre</label>
                <input type="text" id="disabled-input" aria-label="disabled input"
                    class="mb-2 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    value="{{ isset($selectedIngrediente) && is_array($selectedIngrediente) ? $selectedIngrediente['nombre'] : '' }}"
                    disabled>
                @error('selectedIngrediente')
                    <x-input-error messages="{{ $message }}" />
                @enderror
            </div>
            <div class="flex items-end gap-3">
                <div class="w-full">
                    <label for="medida"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Medida</label>
                    <select id="medida" wire:model='selectedMedida'
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500  p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                        <option selected="">Seleccionar</option>
                        <option value="cantidad">Cantidad</option>
                        <option value="peso">Peso</option>
                    </select>
                    @error('selectedMedida')
                        <x-input-error messages="{{ $message }}" />
                    @enderror
                </div>
                <div class="w-full">
                    <label for="cantidad" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"></label>
                    <input type="number" name="cantidad" id="cantidad" wire:model='size'
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600  p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="" required="">
                    @error('size')
                        <x-input-error messages="{{ $message }}" />
                    @enderror
                </div>
            </div>
        </div>
        <button type="button" wire:click='agregarIngrediente'
            class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            <svg class="me-1 -ms-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                    clip-rule="evenodd">
                </path>
            </svg>Añadir
        </button>
    </form>
</div>
