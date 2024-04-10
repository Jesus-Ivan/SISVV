<div class="ms-3 mx-3">
    <div class="flex">
        <div class="relative w-full">
            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                </svg>
            </div>
            <input type="search" id="default-search"
                class="block w-full p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Buscar insumo..." required />
        </div>
        <button type="button"
            class="px-5 py-2.5 text-sm font-medium text-white inline-flex items-center bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 rounded-lg text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800 ms-2">
            Transformar
        </button>
    </div>

    <hr class="h-px my-2 bg-gray-300 border-0 dark:bg-gray-700">

    {{-- Añadir un ingrediente para la transformacion --}}
    <button data-modal-target="crud-modal" data-modal-toggle="crud-modal"
        class="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800"
        type="button">
        Agregar ingrediente
    </button>

    {{-- Tabla con los detalles --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-2">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <caption
                class="p-5 text-lg font-semibold text-left rtl:text-right text-gray-900 bg-white dark:text-white dark:bg-gray-800">
                NOMBRE: SOLOMILLOS
            </caption>
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3 w-96">
                        MATERIA PRIMA
                    </th>
                    <th scope="col" class="px-6 py-3">
                        STOCK
                    </th>
                    <th scope="col" class="px-6 py-3">
                        PESO OCUPADO (KG)
                    </th>
                    <th scope="col" class="px-6 py-3">
                        CANTIDAD OCUPADA (PZ)
                    </th>
                    <th scope="col" class="px-6 py-3">
                        MERMA
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="px-6 py-4 w-96">
                        CAÑA DE FILETE DE RES
                    </td>
                    <td class="px-6 py-4">
                        3.250
                    </td>
                    <td class="px-6 py-4">
                        <input type="number" id="input-peso"
                            class="block w-fit p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs text-center focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    </td>
                    <td class="px-6 py-4">
                        <input type="number" id="input-cantidad"
                            class="block w-fit p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs text-center focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    </td>
                    <td class="px-6 py-4">
                        <input type="number" id="input-merma"
                            class="block w-fit p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs text-center focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <hr class="h-px my-3 bg-gray-300 border-0 dark:bg-gray-700">

    {{-- Detalles de transformación --}}
    <div class="flex justify-center">
        <div class="mb-6">
            <label for="peso-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Peso
                Resultante</label>
            <input type="number" id="peso-input"
                class="me-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-fit p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <label for="peso-input" class="block mb-2 text-sm font-light text-gray-900 dark:text-white">Total obtenido
                en KG</label>
        </div>
        <div class="mb-6">
            <label for="cantidad-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cantidad
                Resultante</label>
            <input type="number" id="cantidad-input"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-fit p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <label for="peso-input" class="block mb-2 text-sm font-light text-gray-900 dark:text-white">Raciones
                Obtenidas</label>
        </div>
    </div>

    {{-- Botones de accion --}}
    <div class="inline-flex flex-grow">
        {{-- Regresar --}}
        <a type="button" href="{{ route('cocina.transformaciones') }}"
            class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
            <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 12h14M5 12l4-4m-4 4 4 4" />
            </svg>Regresar
        </a>
        {{-- Finalizar transformacion --}}
        <button type="button"
            class="px-5 py-2.5 text-sm font-medium text-white inline-flex items-center bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 rounded-lg text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800 ms-2">
            <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 11.917 9.724 16.5 19 7.5" />
            </svg>Finalizar Transformación
        </button>
    </div>


    {{-- Modal para ingredientes --}}
    <div id="crud-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Agregar ingrediente</h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-toggle="crud-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <form class="p-4 md:p-5">
                    <div class="grid gap-4 mb-4 grid-cols-2">
                        <div class="col-span-2">
                            <div class="flex">
                                <button id="dropdown-button" data-dropdown-toggle="dropdown"
                                    class="flex-shrink-0 z-10 inline-flex items-center py-2.5 px-4 text-sm font-medium text-center text-gray-900 bg-gray-100 border border-gray-300 rounded-s-lg hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700 dark:text-white dark:border-gray-600"
                                    type="button">Todos <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 1 4 4 4-4" />
                                    </svg>
                                </button>
                                <div id="dropdown"
                                    class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
                                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200"
                                        aria-labelledby="dropdown-button">
                                        <li>
                                            <button type="button"
                                                class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Insumo
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button"
                                                class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Materia
                                                prima
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                                <div class="relative w-full">
                                    <input type="search" id="search-dropdown"
                                        class="block p-2.5 w-full z-20 text-sm text-gray-900 bg-gray-50 rounded-e-lg border-s-gray-50 border-s-2 border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-s-gray-700  dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:border-blue-500"
                                        placeholder="Buscar nombre" required />
                                </div>
                            </div>
                        </div>
                        <div class="col-span-2">
                            <label for="name"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre</label>
                            <input type="text" id="disabled-input" aria-label="disabled input"
                                class="mb-2 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                value="Nombre del producto" disabled>
                        </div>
                        <div class="flex items-end gap-3">
                            <div class="w-full">
                                <label for="medida"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Medida</label>
                                <select id="medida"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500  p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                    <option selected="">Seleccionar</option>
                                    <option value="c">Cantidad</option>
                                    <option value="p">Peso</option>
                                </select>
                            </div>
                            <div class="w-full">
                                <label for="nombre"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"></label>
                                <input type="number" name="nombre" id="nombre"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600  p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="" required="">
                            </div>
                        </div>
                    </div>
                    <button type="submit"
                        class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        <svg class="me-1 -ms-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                clip-rule="evenodd">
                            </path>
                        </svg>
                        Añadir
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
