<div>
    <div class="ms-3 me-3">
        {{-- Linea divisora --}}
        <hr class="h-px my-1 bg-gray-300 border-0 dark:bg-gray-700">
        <h4 class="flex items-center text-base font-bold dark:text-white">INFORMACIÓN GENERAL</h4>

        <!-- INFORMACION GENERAL -->
        <div class="flex gap-2">
            <!-- NOMBRE -->
            <div class="w-full">
                <label for="nombre" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre</label>
                <input type="text" id="nombre" wire:model="formEdit.nombre"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    required />
                @error('formEdit.nombre')
                    <x-input-error messages="{{ $message }}" />
                @enderror
            </div>
            <!-- FAMILIA -->
            <div class="min-w-72">
                <label for="id_familia"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Familia</label>
                <select id="familia" wire:model='formEdit.familia'
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option selected value="{{ null }}">SELECCIONAR</option>
                    @foreach ($this->clasificacion as $familia)
                        @if ($familia->tipo === 'FAMILIA')
                            <option value="{{ $familia->id }}">{{ $familia->nombre }}</option>
                        @endif
                    @endforeach
                </select>
                @error('formEdit.familia')
                    <x-input-error messages="{{ $message }}" />
                @enderror
            </div>
            <!-- CATEGORIA -->
            <div class="min-w-72">
                <label for="id_categoria"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Categoría</label>
                <select id="categoria" wire:model='formEdit.categoria'
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option selected value="{{ null }}">SELECCIONAR</option>
                    @foreach ($this->clasificacion as $categoria)
                        @if ($categoria->tipo === 'CATEGORÍA')
                            <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                        @endif
                    @endforeach
                </select>
                @error('formEdit.categoria')
                    <x-input-error messages="{{ $message }}" />
                @enderror
            </div>
            <!-- PROVEEDOR -->
            <div class="min-w-72">
                <label for="id_proveedor"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Proveedor</label>
                <select id="proveedor" wire:model='formEdit.proveedor'
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option selected value="{{ null }}">SELECCIONAR</option>
                    @foreach ($this->proveedores as $proveedor)
                        <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                    @endforeach
                </select>
                @error('formEdit.proveedor')
                    <x-input-error messages="{{ $message }}" />
                @enderror
            </div>
        </div>
        <!-- PRECIOS -->
        <div class="my-2 flex gap-2">
            <!-- PRECIO VENTA -->
            <div class="min-w-52">
                <label for="costo_unitario" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Precio
                    Venta</label>
                <input type="number" id="costo_unitario" wire:model="formEdit.costo_unitario"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                @error('formEdit.costo_unitario')
                    <x-input-error messages="{{ $message }}" />
                @enderror
            </div>
            <!-- PRECIO EMPLEADO -->
            <div class="min-w-52">
                <label for="costo_empleado" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Precio
                    Empleado</label>
                <input type="number" id="costo_empleado" wire:model="formEdit.costo_empleado"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            </div>
            <div class="min-w-72">
                <label for="tipo" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipo</label>
                <select id="tipo" wire:model="formEdit.tipo"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option selected value="{{ null }}">SELECCIONAR</option>
                    <option value="INV-VEN" title="Inventariable - Vendible">INV-VEN</option>
                    <option value="INV-NVEN-ABA" title="Inventariable - No Vendible - Abarrotes">INV-NVEN-ABA</option>
                    <option value="INV-NVEN-MP" title="Inventariable - No Vendible - Materia Prima">INV-NVEN-MP</option>
                    <option value="INV-NVEN-SP" title="Inventariable - No Vendible - Semiproducido">INV-NVEN-SP</option>
                    <option value="NINV-VEN-SER" title="No Inventariable - Vendible - Servicios">NINV-VEN-SER</option>
                    <option value="NINV-VEN-PRE-PLAT"
                        title="No Inventariable - Vendible - Producto Preparado - Platillos">NINV-VEN-PRE-PLAT</option>
                    <option value="NINV-VEN-PRE-BEB" title="No Inventariable - Vendible - Producto Preparado - Bebidas">
                        NINV-VEN-PRE-BEB</option>
                    <option value="NINV-NVEN" title="No Inventariable - No vendible">NINV-NVEN</option>
                </select>
            </div>
            <div class="min-w-72">
                <label for="estado"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Estado</label>
                <select id="estado" wire:model="formEdit.estado"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option selected value="{{ null }}">SELECCIONAR</option>
                    <option value="1">ACTIVO</option>
                    <option value="0">INACTIVO</option>
                </select>
                @error('formEdit.estado')
                    <x-input-error messages="{{ $message }}" />
                @enderror
            </div>
        </div>

        {{-- Linea divisora --}}
        <hr class="h-px my-1 bg-gray-300 border-0 dark:bg-gray-700">
        <h4 class="flex items-center text-base font-bold dark:text-white">PRECIO DE COMPRA POR UNIDAD</h4>

        <!-- PRECIO POR UNIDAD-->
        <div class="flex justify-center gap-2 items-end">
            <!-- UNIDAD -->
            <div class="w-fit">
                <label for="unidad"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Unidad</label>
                <select id="id_unidad" wire:model='formEdit.id_unidad'
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option selected value="{{ null }}">SELECCIONAR</option>
                    @foreach ($this->unidades as $unidad)
                        <option value="{{ $unidad->id }}">{{ $unidad->descripcion }}</option>
                    @endforeach
                </select>
            </div>
            <!-- PRECIO -->
            <div class="w-fit">
                <label for="costo_unidad"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Precio</label>
                <input type="number" id="costo" wire:model='formEdit.costo_unidad'
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            </div>
            <!-- BOTON DE AGREGAR -->
            <div>
                <button type="button" wire:click='añadirUnidad'
                    class="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">
                    Agregar
                </button>
            </div>
        </div>
        
        <!-- TABLA DE UNIDADES -->
        <div class="flex justify-center">
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg my-2">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                UNIDAD
                            </th>
                            <th scope="col" class="px-6 py-3">
                                PRECIO
                            </th>
                            <th scope="col" class="px-6 py-3 text-center">
                                ACCIÓN
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($formEdit->unidades as $index => $unidadItem)
                            @if (!array_key_exists('deleted', $unidadItem))
                                <tr
                                    class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                    <th scope="row"
                                        class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        @if (array_key_exists('unidad', $unidadItem))
                                            {{ $unidadItem['unidad']['descripcion'] }}
                                        @else
                                            {{ $this->unidades->find($unidadItem['id_unidad'])->descripcion }}
                                        @endif
                                    </th>
                                    <td class="px-6 py-2">
                                        <div class="flex items-center">
                                            $<input type="number"
                                                wire:model="formEdit.unidades.{{ $index }}.costo_unidad"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-24 p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                                            @error('formEdit.costo_unidad')
                                                <x-input-error messages="{{ $message }}" />
                                            @enderror
                                        </div>
                                    </td>
                                    <td class="px-6 py-2">
                                        <button type="button" wire:click='confirmarEliminar({{ $index }})'
                                            class="text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-3.5 py-1.5 text-center me-2 mb-2 dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                fill="currentColor" class="w-5 h-5">
                                                <path fill-rule="evenodd"
                                                    d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Linea divisora --}}
        <hr class="h-px my-1 bg-gray-300 border-0 dark:bg-gray-700">

        {{-- Boton de regresar --}}
        <a type="button" href="{{ route('almacen.articulos') }}"
            class="my-2 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
            <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 12h14M5 12l4-4m-4 4 4 4" />
            </svg>Cancelar
        </a>
        {{-- Boton de guardar cambios --}}
        <a type="button" wire:click='confirmEdit'
            class="my-2 text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
            <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                height="24" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd"
                    d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7.414A2 2 0 0 0 20.414 6L18 3.586A2 2 0 0 0 16.586 3H5Zm10 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM8 7V5h8v2a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1Z"
                    clip-rule="evenodd" />
            </svg>
            Guardar Cambios
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
</div>
