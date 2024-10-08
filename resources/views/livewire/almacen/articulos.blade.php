<div>
    {{-- Filtro de busqueda --}}
    <div class="relative ms-3 w-96">
        <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                fill="none" viewBox="0 0 20 20">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
            </svg>
        </div>
        <input wire:model.live.debounce.500ms="search" type="search" id="search"
            class="block w-full p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            placeholder="Buscar nombre de artículo o código" required />
    </div>

    {{-- Tabla --}}
    <div class="ms-3 mx-3 my-2">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            CÓDIGO
                        </th>
                        <th scope="col" class="px-10 py-3">
                            FAMILIA
                        </th>
                        <th scope="col" class="px-6 py-3">
                            CATEGORÍA
                        </th>
                        <th scope="col" class="px-20 py-3">
                            NOMBRE
                        </th>
                        <th scope="col" class="px-6 py-3">
                            UNIDAD
                        </th>
                        <th scope="col" class="px-6 py-3">
                            PROVEEDOR
                        </th>
                        <th scope="col" class="px-4 py-3">
                            ALMACÉN
                        </th>
                        <th scope="col" class="px-4 py-3">
                            BAR
                        </th>
                        <th scope="col" class="px-4 py-3">
                            BARRA
                        </th>
                        <th scope="col" class="px-4 py-3">
                            CADDIE
                        </th>
                        <th scope="col" class="px-4 py-3">
                            CAFETERÍA
                        </th>
                        <th scope="col" class="px-4 py-3">
                            COCINA
                        </th>
                        <th scope="col" class="px-6 py-3">
                            COSTO UNITARIO
                        </th>
                        <th scope="col" class="px-6 py-3">
                            ESTADO
                        </th>
                        <th scope="col" class="px-6 py-3">
                            ACCIONES
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($listaArticulos as $catalogo_vista_verde)
                        <tr wire:key={{ $catalogo_vista_verde->codigo }}
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4">
                                {{ $catalogo_vista_verde->codigo }}
                            </td>
                            <td class="px-6 py-4 uppercase">
                                {{ $catalogo_vista_verde->familia }}
                            </td>
                            <td class="px-6 py-4 uppercase">
                                {{ $catalogo_vista_verde->categoria }}
                            </td>
                            <td class="px-6 py-4 uppercase">
                                {{ $catalogo_vista_verde->nombre }}
                            </td>
                            <td class="px-6 py-4 uppercase">
                                {{ $catalogo_vista_verde->unidad }}
                            </td>
                            <td class="px-6 py-4 uppercase">
                                {{ $catalogo_vista_verde->proveedor }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                    {{ $catalogo_vista_verde->stock_amc }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">1
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">1
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">1
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">1
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">1
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                ${{ $catalogo_vista_verde->costo_unitario}}
                            </td>
                            <td class="px-6 py-4">
                                @if ($catalogo_vista_verde->estado == '1')
                                    <span
                                        class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">ACTIVO
                                    </span>
                                @else
                                    <span
                                        class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">INACTIVO
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if ($catalogo_vista_verde->estado == '1')
                                    <div class="flex">
                                        <button type="button"
                                            wire:click="edit({{ $catalogo_vista_verde->codigo }})"
                                            class="text-green-700 hover:text-white border border-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-3.5 py-1.5 text-center me-2 mb-2 dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-600 dark:focus:ring-green-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                fill="currentColor" class="w-5 h-5">
                                                <path
                                                    d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z" />
                                                <path
                                                    d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z" />
                                            </svg>
                                        </button>
                                        <button type="button"
                                            wire:click="delete({{ $catalogo_vista_verde->codigo }})"
                                            class="text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-3.5 py-1.5 text-center me-2 mb-2 dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                fill="currentColor" class="w-5 h-5">
                                                <path fill-rule="evenodd"
                                                    d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                @else
                                    <div class="flex">
                                        <button type="button"
                                            wire:click="reingresar({{ $catalogo_vista_verde->codigo }})"
                                            class="text-gray-700 hover:text-white border border-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-3.5 py-1.5 text-center me-2 mb-2 dark:border-gray-500 dark:text-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-900">
                                            <svg class="w-5 h-5" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M5.027 10.9a8.729 8.729 0 0 1 6.422-3.62v-1.2A2.061 2.061 0 0 1 12.61 4.2a1.986 1.986 0 0 1 2.104.23l5.491 4.308a2.11 2.11 0 0 1 .588 2.566 2.109 2.109 0 0 1-.588.734l-5.489 4.308a1.983 1.983 0 0 1-2.104.228 2.065 2.065 0 0 1-1.16-1.876v-.942c-5.33 1.284-6.212 5.251-6.25 5.441a1 1 0 0 1-.923.806h-.06a1.003 1.003 0 0 1-.955-.7A10.221 10.221 0 0 1 5.027 10.9Z" />
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="w-full my-2 flex justify-end">{{ $listaArticulos->links() }}</div>
    </div>

    {{-- Modal para añadir articulo --}}
    <x-modal name="añadir" title="AÑADIR NUEVO ARTICULO">
        <x-slot:body>
            <form class="p-4 md:p-5">
                <div class="grid gap-4 mb-4 grid-cols-4">
                    <div class="col-span-1 sm:col-span-1">
                        <label for="codigo"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Código</label>
                        <input type="number" id="disabled-input" aria-label="disabled input"
                            class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            disabled>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="familia"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Familia</label>
                        <select id="familia" wire:model="id_familia"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option selected value="{{ null }}">Seleccionar Familia</option>
                            @foreach ($this->familias as $familia)
                                <option value="{{ $familia->id }}">{{ $familia->familia }}</option>
                            @endforeach
                        </select>
                        @error('id_familia')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="categoria"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Categoría</label>
                        <select id="categoria" wire:model="id_categoria"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option selected value="{{ null }}">Seleccionar Categoría</option>
                            @foreach ($this->categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->categoria }}</option>
                            @endforeach
                        </select>
                        @error('id_categoria')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="proveedor"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Proveedor</label>
                        <select id="proveedor" wire:model="id_proveedor"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option selected value="{{ null }}">Seleccionar Proveedor</option>
                            @foreach ($this->proveedores as $proveedor)
                                <option value="{{ $proveedor->id }}">{{ $proveedor->proveedor }}</option>
                            @endforeach
                        </select>
                        @error('id_proveedor')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                    <div class="col-span-4">
                        <label for="nombre_articulo"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre del
                            producto</label>
                        <input type="text" name="nombre_articulo" id="nombre_articulo" wire:model="nombre"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Nombre">
                        @error('nombre')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="unidad"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Unidad de
                            medida</label>
                        <select id="unidad" wire:model="id_unidad"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option selected value="{{ null }}">Seleccionar Unidad</option>
                            @foreach ($this->unidades as $unidad)
                                <option value="{{ $unidad->id }}">{{ $unidad->unidad }}</option>
                            @endforeach
                        </select>
                        @error('id_unidad')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                    <div class="col-span-1 sm:col-span-1">
                        <label for="precio"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Costo Unitario</label>
                        <input type="number" name="precio" id="precio" wire:model="costo_unitario"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Precio">
                        @error('costo_unitario')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                    <div class="col-span-1 sm:col-span-1">
                        <label for="st_min_amc"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Stock Mínimo</label>
                        <input type="number" name="st_min_amc" id="st_min_amc" wire:model="st_min_amc"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Mínimo">
                        @error('st_min_amc_amc')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                    <div class="col-span-1 sm:col-span-1">
                        <label for="st_max_amc"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Stock Maximo</label>
                        <input type="number" name="st_max_amc" id="st_max_amc" wire:model="st_max_amc"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Máximo">
                        @error('st_max_amc')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                </div>
            </form>
        </x-slot>
        <x-slot:footer>
            <button type="button" wire:click="register"
                class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Aceptar
            </button>
            <button x-on:click="show = false" type="button"
                class="py-2.5 px-5 ms-3 text-sm font-medium text-red-900 focus:outline-none bg-red rounded-lg border border-red-200 hover:bg-red-100 hover:text-red-700 focus:z-10 focus:ring-4 focus:ring-red-100 dark:focus:ring-red-700 dark:bg-red-800 dark:text-red-400 dark:border-red-600 dark:hover:text-white dark:hover:bg-red-700">Cancelar
            </button>
        </x-slot>
    </x-modal>

    {{-- Modal para modificar un articulo --}}
    @if ($catalogoVV)
        <x-modal name="modificarAr" title="MODIFICAR ARTICULO">
            <x-slot:body>
                <form class="p-4 md:p-5">
                    <div class="grid gap-4 mb-4 grid-cols-4">
                        <div class="col-span-1 sm:col-span-1">
                            <label for="codigo"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Código</label>
                            <input type="number" id="disabled-input" aria-label="disabled input"
                                wire:model="codigo"
                                class=" bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                value="codigo" disabled>
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label for="editar_familia"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Familia</label>
                            <select id="editar_familia" wire:model="id_familia"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option selected value="{{ null }}">Seleccionar Familia</option>
                                @foreach ($this->familias as $familia)
                                    <option value="{{ $familia->id }}">{{ $familia->familia }}</option>
                                @endforeach
                            </select>
                            @error('id_familia')
                                <x-input-error messages="{{ $message }}" />
                            @enderror
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label for="editar_categoria"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Categoría</label>
                            <select id="editar_categoria" wire:model="id_categoria"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option selected value="{{ null }}">Seleccionar Categoría</option>
                                @foreach ($this->categorias as $categoria)
                                    <option value="{{ $categoria->id }}">{{ $categoria->categoria }}</option>
                                @endforeach
                            </select>
                            @error('id_categoria')
                                <x-input-error messages="{{ $message }}" />
                            @enderror
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label for="editar_proveedor"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Proveedor</label>
                            <select id="editar_proveedor" wire:model="id_proveedor"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option selected value="{{ null }}">Seleccionar Proveedor</option>
                                @foreach ($this->proveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}">{{ $proveedor->proveedor }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-4">
                            <label for="editar_nombre" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre del producto</label>
                            <input type="text" name="editar_nombre" id="editar_nombre" wire:model="nombre"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Nombre">
                            @error('nombre')
                                <x-input-error messages="{{ $message }}" />
                            @enderror
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label for="editar_unidad"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Unidad de
                                medida</label>
                            <select id="editar_unidad" wire:model="id_unidad"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option selected value="{{ null }}">Seleccionar Unidad</option>
                                @foreach ($this->unidades as $unidad)
                                    <option value="{{ $unidad->id }}">{{ $unidad->unidad }}</option>
                                @endforeach
                            </select>
                            @error('id_unidad')
                                <x-input-error messages="{{ $message }}" />
                            @enderror
                        </div>
                        <div class="col-span-1 sm:col-span-1">
                            <label for="editar_precio"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Costo
                                Unitario</label>
                            <input type="number" name="editar_precio" id="editar_precio"
                                wire:model="costo_unitario"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Precio">
                            @error('costo_unitario')
                                <x-input-error messages="{{ $message }}" />
                            @enderror
                        </div>
                        <div class="col-span-1 sm:col-span-1">
                            <label for="editar_st_min_amc"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Stock
                                Mínimo</label>
                            <input type="number" name="editar_st_min_amc" id="editar_st_min_amc" wire:model="st_min_amc"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Mínimo">
                            @error('st_min_amc')
                                <x-input-error messages="{{ $message }}" />
                            @enderror
                        </div>
                        <div class="col-span-1 sm:col-span-1">
                            <label for="editar_st_max_amc"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Stock
                                Maximo</label>
                            <input type="number" name="editar_st_max_amc" id="editar_st_max_amc" wire:model="st_max_amc"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Máximo">
                            @error('st_max_amc')
                                <x-input-error messages="{{ $message }}" />
                            @enderror
                        </div>
                    </div>
                </form>
            </x-slot>
            <x-slot:footer>
                <button type="button" wire:click="updateAr()"
                    class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Modificar
                </button>
                <button wire:click="cancelarEdit" x-on:click="show = false" type="button"
                    class="py-2.5 px-5 ms-3 text-sm font-medium text-red-900 focus:outline-none bg-red rounded-lg border border-red-200 hover:bg-red-100 hover:text-red-700 focus:z-10 focus:ring-4 focus:ring-red-100 dark:focus:ring-red-700 dark:bg-red-800 dark:text-red-400 dark:border-red-600 dark:hover:text-white dark:hover:bg-red-700">Cancelar
                </button>
            </x-slot>
        </x-modal>
    @endif

    {{-- Modal para eliminar articulo --}}
    @if ($catalogoVV)
        <x-modal name="eliminarAr" title="ELIMINAR ARTÍCULO">
            <x-slot:body>
                <div class="text-center">
                    <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <h3 class="mb-5 text-xl font-normal text-gray-500 dark:text-gray-400">¿Desea eliminar
                        {{ $catalogo_vista_verde->nombre }}?
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400">El artículo no se eliminara de la base de datos, solo
                        pasara a un estado inactivo.</p>
                </div>
            </x-slot>
            <x-slot:footer>
                <button type="button" wire:click="confirmDelete()"
                    class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                    Desactivar
                </button>
                <button type="button" x-on:click="show = false"
                    class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Cancelar</button>
            </x-slot>
        </x-modal>
    @endif

    {{-- Modal para reingresar la categoria --}}
    @if ($catalogoVV)
        <x-modal name="reingresarAr" title="REINGRESAR ARTÍCULO">
            <x-slot:body>
                <div class="text-center">
                    <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <h3 class="mb-5 text-xl font-normal text-gray-500 dark:text-gray-400">¿Desea reingresar
                        {{ $catalogo_vista_verde->nombre }}?
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400">El artículo pasará nuevamente a un estado Activo</p>
                </div>
            </x-slot>
            <x-slot:footer>
                <button type="button" wire:click="confirmReingreso()"
                    class="text-white bg-green-600 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">Reingresar
                </button>
                <button type="button" x-on:click="show = false"
                    class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Cancelar</button>
            </x-slot>
        </x-modal>
    @endif

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