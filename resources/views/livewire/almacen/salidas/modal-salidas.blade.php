<div>
    <form class="p-1 md:p-1">
        <div class="grid gap-1 mb-1 grid-cols-3">
            {{-- BARRA DE BUSQUEDA --}}
            <div class="col-span-3">
                <label for="name" class="mb-2 text-sm font-medium text-gray-900 dark:text-white">Buscar articulo</label>
                <livewire:autocomplete :params="['table_name' => 'ipa_inventario_principal', 'columns' => ['nombre', 'codigo']]" event="on-selected-articulo" primary='codigo' />
                
            </div>
            {{-- CODIGO DE ARTICULO --}}
            <div class="col-span-1">
                <label for="codigo" class="mb-2 text-sm font-medium text-gray-900 dark:text-white">Código</label>
                <input type="text" id="codigo" aria-label="codigo" 
                    class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" value="{{ ($articuloSeleccionado) ? $articuloSeleccionado['codigo'] : '' }}" disabled>
            </div>
            {{-- NOMBRE DE ARTICULO --}}
            <div class="col-span-2">
                <label for="nombre" class="mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre</label>
                <input type="text" id="nombre" aria-label="nombre" 
                    class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" value="{{ ($articuloSeleccionado) ? $articuloSeleccionado['nombre'] : '' }}" disabled>
            </div>
            {{-- ORIGEN DE SALIDA --}}
            <div class="col-span-1">
                <label for="salida" class="mb-2 text-sm font-medium text-gray-900 dark:text-white">Origen Salida</label>
                <input type="text" id="disabled-input" aria-label="disabled input"
                    class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    value="Almacén" disabled>
            </div>
            {{-- EXISTENCIAS EN EL ORIGEN --}}
            <div class="col-span-1">
                <label for="stock" class="mb-2 text-sm font-medium text-gray-900 dark:text-white">Existencias</label>
                <input type="text" id="disabled-input" aria-label="disabled input"
                    class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" value="{{ ($articuloSeleccionado) ? $articuloSeleccionado['stock'] : '' }}" disabled>
            </div>
            {{-- CANTIDAD PARA DAR SALIDA --}}
            <div class="col-span-1 sm:col-span-1">
                <label for="cantidad" class="mb-2 text-sm font-medium text-gray-900 dark:text-white">Cantidad</label>
                <input type="number" name="cantidad" id="cantidad" wire:model="cantidad"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    placeholder="Cantidad" required>
                @error('cantidad')
                    <x-input-error messages="{{ $message }}" />
                @enderror
            </div>
        </div>
    </form>
    {{-- MODAL FOOTER --}}
    <button type="button" wire:click = 'agregarSalida'
        class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Agregar
    </button>
</div>
