<div>
    {{-- DATOS DE LA VENTA --}}
    <div>
        <h6 class="text-lg font-bold dark:text-white">Datos de la venta</h6>
        <hr class="h-1 my-2 bg-gray-200 border-0 dark:bg-gray-700">
        <div class="flex items-end gap-4">
            {{-- Punto de venta --}}
            <div class="w-64">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Selecciona punto de
                    venta</label>
                <select
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option selected>Choose a country</option>
                    <option value="US">United States</option>
                    <option value="CA">Canada</option>
                    <option value="FR">France</option>
                    <option value="DE">Germany</option>
                </select>
            </div>
            {{-- Corte de caja --}}
            <input type="text" placeholder="Corte de caja"
                class="h-10 block max-w-32 p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <div>
                {{-- Total Original --}}
                <p>Total original: </p>
                {{-- Fecha apertura --}}
                <p>Fecha apertura: </p>
                {{-- Fecha cierre --}}
                <p>Fecha cierre: </p>
            </div>
        </div>
    </div>
    {{-- DATOS DEL SOCIO --}}
    <div>
        <h6 class="text-lg font-bold dark:text-white">Datos del socio</h6>
        <hr class="h-1 my-2 bg-gray-200 border-0 dark:bg-gray-700">
        <div class="flex gap-2 items-end">
            {{-- TITULAR ACTUAL DE LA COMPRA --}}
            <div class="w-1/3">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Titular actual</label>
                <input type="text" aria-label="disabled input 2"
                    class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    value="Disabled readonly input" disabled readonly>
            </div>
            {{-- NUEVO TITULAR DE LA COMPRA --}}
            <div class="w-1/3">
                <livewire:autocomplete :params="[
                    'table' => ['name' => 'socios', 'columns' => ['id', 'nombre', 'apellido_p', 'apellido_m']],
                ]" primaryKey="id" event="on-selected-socio" />
            </div>
            <div class="flex w-1/3 items-center">
                <p class="w-full">Nuevo titular:</p>
                <button type="button"
                    class="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2 text-center dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">Limpiar</button>
            </div>
        </div>
    </div>
    {{-- PRODUCTOS DE LA VENTA --}}
    <div>
        <h6 class="text-lg font-bold dark:text-white">Productos</h6>
        <hr class="h-1 my-2 bg-gray-200 border-0 dark:bg-gray-700">
        {{-- Tabla de edicion --}}
        <div class="max-h-96 relative overflow-y-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            DESCRIPCION
                        </th>
                        <th scope="col" class="px-6 py-3">
                            CANTIDAD
                        </th>
                        <th scope="col" class="px-6 py-3">
                            PRECIO UNITARIO
                        </th>
                        <th scope="col" class="px-6 py-3">
                            IMPORTE
                        </th>
                        <th scope="col" class="px-6 py-3">
                            ACCIONES
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                        <th scope="row"
                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            Apple MacBook Pro 17"
                        </th>
                        <td class="px-6 py-4">
                            Silver
                        </td>
                        <td class="px-6 py-4">
                            Laptop
                        </td>
                        <td class="px-6 py-4 flex items-center gap-2">
                            $<input type="text" id="small-input"
                                class="block max-w-32 p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </td>
                        <td class="px-6 py-4">
                            <a href="#"
                                class="font-medium text-red-600 dark:text-red-500 hover:underline">Eliminar</a>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <th>Total: $1.0</th>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    {{-- PAGOS DE LA VENTA  --}}
    <div>
        <h6 class="text-lg font-bold dark:text-white">Metodo de pago</h6>
        <hr class="h-1 my-2 bg-gray-200 border-0 dark:bg-gray-700">
        {{-- Tabla --}}
        <div class="max-h-96 relative overflow-y-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class=" px-6 py-3">
                            No.socio
                        </th>
                        <th scope="col" class="w-3/6 px-6 py-3">
                            Nombre
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Metodo pago
                        </th>
                        <th scope="col" class="px-6 py-3">
                            IMPORTE
                        </th>
                        <th scope="col" class="px-6 py-3">
                            ACCIONES
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                        <th scope="row"
                            class=" px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            <input type="text"
                                class="block max-w-32 p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </th>
                        <td class="px-6 py-4">
                            <input type="text"
                                class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </td>
                        <td class="px-6 py-4">
                            <select
                                class="block p-2  text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option selected>Choose a country</option>
                                <option value="US">United States</option>
                                <option value="CA">Canada</option>
                                <option value="FR">France</option>
                                <option value="DE">Germany</option>
                            </select>
                        </td>
                        <td class="px-6 py-4 flex items-center gap-2">
                            $<input type="text" id="small-input"
                                class="block max-w-32 p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </td>
                        <td class="px-6 py-4">
                            <a href="#"
                                class="font-medium text-red-600 dark:text-red-500 hover:underline">Eliminar</a>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <th>Total: $1.0</th>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    {{-- TIPO DE CORRECCION --}}
    <div>
        <h6 class="text-lg font-bold dark:text-white">Datos de la correccion</h6>
        <hr class="h-1 my-2 bg-gray-200 border-0 dark:bg-gray-700">
        <div class="flex">
            {{-- Solicitante de la correccion --}}
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Solicitante</label>
                <select
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option selected>Seleccione</option>
                    <option value="US">United States</option>
                </select>
            </div>
            {{-- Motivo de correccion --}}
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Motivo de correccion</label>
                <ul
                    class="items-center w-full text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg sm:flex dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r dark:border-gray-600">
                        <div class="flex items-center ps-3">
                            <input id="vue-checkbox-list" type="checkbox" value=""
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                            <label for="vue-checkbox-list"
                                class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Vue
                                JS</label>
                        </div>
                    </li>
                    <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r dark:border-gray-600">
                        <div class="flex items-center ps-3">
                            <input id="react-checkbox-list" type="checkbox" value=""
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                            <label for="react-checkbox-list"
                                class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">React</label>
                        </div>
                    </li>
                    <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r dark:border-gray-600">
                        <div class="flex items-center ps-3">
                            <input id="angular-checkbox-list" type="checkbox" value=""
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                            <label for="angular-checkbox-list"
                                class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Angular</label>
                        </div>
                    </li>
                    <li class="w-full dark:border-gray-600">
                        <div class="flex items-center ps-3">
                            <input id="laravel-checkbox-list" type="checkbox" value=""
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                            <label for="laravel-checkbox-list"
                                class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Laravel</label>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Fecha actual de correccion --}}
    </div>
</div>
