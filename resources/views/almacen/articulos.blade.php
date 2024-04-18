<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('almacen.nav')
    </x-slot>

    {{-- Contenido --}}
    <div class="container py-5">
        <div class="flex ms-3">
            <button x-data x-on:click="$dispatch('open-modal', { name: 'añadir' })"
                class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                type="button">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
            </button>
            <h4 class="flex items-center ms-2 text-2xl font-bold dark:text-white">Lista de productos</h4>
        </div>
    </div>

    <div>
        <livewire:almacen.tabla-articulos />
    </div>

    <div class="ms-3 mx-3">
        <hr class="h-px my-4 bg-gray-300 border-0 dark:bg-gray-700">

        <div
            class="max-w-lg p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
            <h2 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">INDICADORES</h2>
            <ul class="max-w-xl space-y-1 text-gray-500 list-inside dark:text-gray-400">
                <li>
                    <span
                        class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Verde
                    </span>Stock adecuado o artículo activo.
                </li>
                <li>
                    <span
                        class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Rojo
                    </span>Stock vacío o artículo inactivo.
                </li>
                <li>
                    <span
                        class="bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">Amarillo
                    </span>Sugerencia para compra o surtido a puntos de venta.
                </li>
            </ul>
        </div>
    </div>

    {{--Modal para añadir articulo--}}
    <x-modal name="añadir" title="AÑADIR NUEVO ARTICULO">
        <x-slot:body>
            <form class="p-4 md:p-5">
                <div class="grid gap-4 mb-4 grid-cols-4">
                    <div class="col-span-1 sm:col-span-1">
                        <label for="codigo"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Código</label>
                        <input type="number" id="disabled-input" aria-label="disabled input"
                            class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            value="codigo" disabled>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="familia"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Familia</label>
                        <select id="familia"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option selected="">Seleccionar Familia</option>
                            <option value="TV">TV/Monitors</option>
                            <option value="PC">PC</option>
                            <option value="GA">Gaming/Console</option>
                            <option value="PH">Phones</option>
                        </select>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="categoria"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Categoría</label>
                        <select id="categoria"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option selected="">Seleccionar Categoría</option>
                            <option value="TV">TV/Monitors</option>
                            <option value="PC">PC</option>
                            <option value="GA">Gaming/Console</option>
                            <option value="PH">Phones</option>
                        </select>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="proveedor"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Proveedor</label>
                        <select id="proveedor"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option selected="">Seleccionar Proveedor</option>
                            <option value="TV">TV/Monitors</option>
                            <option value="PC">PC</option>
                            <option value="GA">Gaming/Console</option>
                            <option value="PH">Phones</option>
                        </select>
                    </div>

                    <div class="col-span-4">
                        <label for="name"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre del
                            producto</label>
                        <input type="text" name="name" id="name"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Nombre" required="">
                    </div>

                    <div class="col-span-2 sm:col-span-1">
                        <label for="unidad"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Unidad de
                            medida</label>
                        <select id="unidad"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option selected="">Seleccionar Unidad</option>
                            <option value="TV">TV/Monitors</option>
                            <option value="PC">PC</option>
                            <option value="GA">Gaming/Console</option>
                            <option value="PH">Phones</option>
                        </select>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="departamento"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Departamento</label>
                        <select id="departamento"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option selected="">Seleccionar Departamento</option>
                            <option value="TV">TV/Monitors</option>
                            <option value="PC">PC</option>
                            <option value="GA">Gaming/Console</option>
                            <option value="PH">Phones</option>
                        </select>
                    </div>
                    <div class="col-span-1 sm:col-span-1">
                        <label for="precio" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Costo
                            Unitario</label>
                        <input type="number" name="precio" id="precio"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Precio" required="">
                    </div>

                    <div class="col-span-1 sm:col-span-1">
                        <label for="st_min"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Stock
                            Mínimo</label>
                        <input type="number" name="st_min" id="st_min"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Mínimo" required="">
                    </div>
                    <div class="col-span-1 sm:col-span-1">
                        <label for="st_max"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Stock
                            Maximo</label>
                        <input type="number" name="st_max" id="st_max"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Máximo" required="">
                    </div>
                </div>
            </form>
        </x-slot>
        <x-slot:footer>
            <button type="button"
                class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Aceptar
            </button>
            <button x-on:click="show = false" type="button"
                class="py-2.5 px-5 ms-3 text-sm font-medium text-red-900 focus:outline-none bg-red rounded-lg border border-red-200 hover:bg-red-100 hover:text-red-700 focus:z-10 focus:ring-4 focus:ring-red-100 dark:focus:ring-red-700 dark:bg-red-800 dark:text-red-400 dark:border-red-600 dark:hover:text-white dark:hover:bg-red-700">Cancelar
            </button>
        </x-slot>
    </x-modal>
</x-app-layout>
