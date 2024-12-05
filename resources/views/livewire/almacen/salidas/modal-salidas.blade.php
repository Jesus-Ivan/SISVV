<div>
    <form class="mb-2">
        <div class="grid gap-1 mb-1 grid-cols-3">
            {{-- BARRA DE BUSQUEDA --}}
            <div class="col-span-3">
                <label for="name" class="mb-2 text-sm font-medium text-gray-900 dark:text-white">Buscar
                    articulo</label>
                <livewire:search-input :params="[
                    'tittle_bar' => 'Codigo o nombre articulo',
                    'table_name' => 'catalogo_vista_verde',
                    'table_columns' => ['codigo', 'nombre'],
                    'primary_key' => 'codigo',
                    'event' => 'selected-articulo',
                    'args' => 'INV%',
                ]" />

            </div>
            {{-- CODIGO DE ARTICULO --}}
            <div class="col-span-1">
                <label for="codigo" class="mb-2 text-sm font-medium text-gray-900 dark:text-white">Código</label>
                <input type="text" id="codigo" aria-label="codigo"
                    class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    value="{{ $articulo_seleccionado ? $articulo_seleccionado['codigo'] : '' }}" disabled>
            </div>
            {{-- NOMBRE DE ARTICULO --}}
            <div class="col-span-2">
                <label for="nombre" class="mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre</label>
                <input type="text" id="nombre" aria-label="nombre"
                    class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    value="{{ $articulo_seleccionado ? $articulo_seleccionado['nombre'] : '' }}" disabled>
            </div>
        </div>
        <div class="flex gap-2 items-center">
            {{-- EXISTENCIAS EN EL ORIGEN (Unitario) --}}
            <div>
                <label for="stock" class="mb-2 text-sm font-medium text-gray-900 dark:text-white">Stock
                    Unitario</label>
                <input type="text" id="disabled-input" aria-label="disabled input"
                    class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    value="{{ $cantidad_stock ? $cantidad_stock->$clave_stock_origen : '' }}" disabled>
            </div>
            {{-- EXISTENCIAS EN EL ORIGEN (Peso) --}}
            <div>
                <label for="stock" class="mb-2 text-sm font-medium text-gray-900 dark:text-white">Stock Peso</label>
                <input type="text" id="disabled-input" aria-label="disabled input"
                    class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    value="{{ $peso_stock ? $peso_stock->$clave_stock_origen : '' }}" disabled>
            </div>
            {{-- CANTIDAD PARA DAR SALIDA (cantidad) --}}
            <div class="col-span-1 sm:col-span-1">
                <label for="cantidad" class="mb-2 text-sm font-medium text-gray-900 dark:text-white">Cantidad</label>
                <input type="number" name="cantidad" id="cantidad" wire:model="cantidad_salida"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    placeholder="Cantidad">
            </div>
            {{-- CANTIDAD PARA DAR SALIDA (peso) --}}
            <div class="col-span-1 sm:col-span-1">
                <label for="cantidad" class="mb-2 text-sm font-medium text-gray-900 dark:text-white">Peso</label>
                <input type="number" name="cantidad" id="cantidad" wire:model="peso_salida"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    placeholder="Peso">
            </div>
        </div>
    </form>
    {{-- MODAL FOOTER --}}
    <button type="button" wire:click = 'agregarSalida'
        class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Agregar
    </button>
</div>
