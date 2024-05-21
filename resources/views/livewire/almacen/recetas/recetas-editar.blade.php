<div>
    <!-- Registro para editar platillo -->
    <div class="ms-3 mx-3">
        <!-- Inputs -->
        <div class="flex gap-1">
            <div class="w-full">
                <label for="nombre-platillo" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre
                    del platillo:</label>
                <input type="text" id="nombre" wire:model='nombre'
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                @error('nombre')
                    <x-input-error messages="{{ $message }}" />
                @enderror
            </div>
            <div class="w-full">
                <label for="descripcion-platillo"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Descripción (Opcional):</label>
                <input type="text" id="descripcion" wire:model='descripcion'
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            </div>
            <div class="w-fit">
                <label for="categorias"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Categoría:</label>
                <select id="categorias" wire:model='categoria'
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-fit p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option>SELECCIONAR</option>
                    <option value="CATEGORIA CON NOMBRE LARGO">CATEGORIA CON NOMBRE LARGO </option>
                    <option value="SABOR A MEXICO">SABOR A MEXICO</option>
                    <option value="DE LA GRANJA">DE LA GRANJA</option>
                    <option value="PIZZAS">PIZZAS</option>
                    <option value="DEL MAR">DEL MAR</option>
                </select>
                @error('categoria')
                    <x-input-error messages="{{ $message }}" />
                @enderror
            </div>
            <div class="w-fit">
                <label for="porciones"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Porción:</label>
                <select id="porciones" wire:model='porcion'
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-fit p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option>SELECCIONAR</option>
                    <option>180 GRAMOS</option>
                    <option>250 ML</option>
                    <option>4 PIEZAS</option>
                    <option>1 PORCIÓN</option>
                </select>
                @error('porcion')
                    <x-input-error messages="{{ $message }}" />
                @enderror
            </div>
        </div>

        <hr class="h-px my-4 bg-gray-300 border-0 dark:bg-gray-700">

        <button x-data x-on:click="$dispatch('open-modal', { name: 'ingredientes' })"
            class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
            type="button">
            Agregar ingrediente
        </button>

        {{-- Tabla con los detalles --}}
        <div class="relative overflow-y-auto h-80 shadow-md sm:rounded-lg mt-3">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            CÓDIGO
                        </th>
                        <th scope="col" class="px-6 py-3 w-full">
                            NOMBRE
                        </th>
                        <th scope="col" class="px-6 py-3 min-w-80">
                            CANTIDAD(PZ)
                        </th>
                        <th scope="col" class="px-6 py-3 min-w-40">
                            PESO(KG)
                        </th>
                        <th scope="col" class="px-6 py-3 max-w-fit">

                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($listaIngredientes as $index => $ingrediente)
                        <tr
                            class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                            <td class="px-6 py-4">
                                {{ $ingrediente['codigo'] }}
                            </td>
                            <td class="px-6 py-4 uppercase">
                                {{ $ingrediente['nombre'] }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $ingrediente['medida'] == 'cantidad' ? $ingrediente['size'] : 0 }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $ingrediente['medida'] == 'peso' ? $ingrediente['size'] : 0 }}
                            </td>
                            <td class="max-w-fit px-6 py-4">
                                <button type="button" wire:click="remove({{ $index }})"
                                    class="text-red-700 border border-red-700 hover:bg-red-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:focus:ring-red-800 dark:hover:bg-red-500">
                                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                                    </svg>
                                    <span class="sr-only">Eliminar</span>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <form class="ms-3 my-3 mx-auto">
        <label for="precio_venta" class="mb-2 text-sm font-medium text-gray-900 dark:text-white">Precio para carta:</label>
        <input type="number" id="precio_venta" wire:model='precio_venta'
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-32 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            placeholder="Precio" required />
        @error('precio_venta')
            <x-input-error messages="{{ $message }}" />
        @enderror
    </form>

    {{-- Linea divisra y botones de accion --}}
    <div class="ms-3 mx-3">
        <hr class="h-px my-4 bg-gray-300 border-0 dark:bg-gray-700">

        {{-- Botones de accion --}}
        <div class="inline-flex flex-grow">
            <a type="button" href="{{ route('almacen.recetas') }}"
                class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                    height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 12h14M5 12l4-4m-4 4 4 4" />
                </svg>Cancelar
            </a>
            <button type="button" wire:click='saveReceta'
                class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                    height="24" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M7.833 2c-.507 0-.98.216-1.318.576A1.92 1.92 0 0 0 6 3.89V21a1 1 0 0 0 1.625.78L12 18.28l4.375 3.5A1 1 0 0 0 18 21V3.889c0-.481-.178-.954-.515-1.313A1.808 1.808 0 0 0 16.167 2H7.833Z" />
                </svg>Guardar Receta
            </button>
        </div>
    </div>

    {{-- Modal para ingredientes --}}
    <x-modal name="ingredientes" title="AGREGAR INGREDIENTE">
        {{-- MODAL BODY --}}
        <x-slot:body>
            <livewire:almacen.recetas.modal-ingredientes />
        </x-slot>
    </x-modal>

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
</div>
