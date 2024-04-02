<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('almacen.nav')
    </x-slot>

    {{-- Contenido --}}
    <div class="container py-5">
        <div class="flex ms-3">
            <div class="inline-flex flex-grow">
                <button data-modal-target="large-modal" data-modal-toggle="large-modal"
                    class="block w-full md:w-auto text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                    type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path fill-rule="evenodd"
                            d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <h4 class="flex items-center ms-2 text-2xl font-bold dark:text-white">Nueva orden de compra</h4>
            </div>

            <div class="inline-flex">
                <label for="name" class="flex items-center text-sm font-medium text-gray-900 dark:text-white">FECHA:
                </label>
                <input type="text" id="disabled-input" aria-label="disabled input"
                    class="ms-2 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    value="Fecha" disabled>
            </div>
            <div class="inline-flex">
                <label for="name" class="ms-2 flex items-center text-sm font-medium text-gray-900 dark:text-white">FOLIO:
                </label>
                <input type="text" id="disabled-input" aria-label="disabled input"
                    class="ms-2 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    value="Folio" disabled>
            </div>
        </div>
    </div>

    <div class="container py-5">
        <div class="flex ms-3">
            <div class="inline-flex flex-grow gap-3">
                <label for="departamento"
                    class="flex items-center mb-2 text-lg font-medium text-gray-900 dark:text-white">Tipo de
                    orden:</label>
                <select id="departamento"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 w-fit p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                    <option selected="">Seleccionar Tipo</option>
                    <option value="TV">General</option>
                    <option value="PC">Evento</option>
                </select>

                <button type="button"
                    class="px-5 py-2.5 text-sm font-medium text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path
                            d="M19.5 21a3 3 0 0 0 3-3v-4.5a3 3 0 0 0-3-3h-15a3 3 0 0 0-3 3V18a3 3 0 0 0 3 3h15ZM1.5 10.146V6a3 3 0 0 1 3-3h5.379a2.25 2.25 0 0 1 1.59.659l2.122 2.121c.14.141.331.22.53.22H19.5a3 3 0 0 1 3 3v1.146A4.483 4.483 0 0 0 19.5 9h-15a4.483 4.483 0 0 0-3 1.146Z" />
                    </svg>
                    Guardar orden
                </button>
            </div>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="ms-3 mx-3">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            CÓDIGO
                        </th>
                        <th scope="col" class="px-16 py-3">
                            DESCRIPCIÓN
                        </th>
                        <th scope="col" class="px-4 py-3">
                            UNIDAD
                        </th>
                        <th scope="col" class="px-6 py-3">
                            PROVEEDOR
                        </th>
                        <th scope="col" class="px-6 py-3">
                            CANTIDAD
                        </th>
                        <th scope="col" class="px-6 py-3">
                            COSTO UNITARIO
                        </th>
                        <th scope="col" class="px-6 py-3">
                            IMPORTE
                        </th>
                        <th scope="col" class="px-6 py-3">
                            IVA
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
                            ULTIMA COMPRA
                        </th>
                        <th scope="col" class="px-6 py-3">
                            ACCIONES
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-4">
                            1234
                        </td>
                        <td class="px-6 py-4">
                            BOTELLA BACARDI 950ML
                        </td>
                        <td class="px-6 py-4">
                            PIEZA
                        </td>
                        <td class="px-6 py-4">
                            VINOLESA
                        </td>
                        <td class="px-6 py-4">
                            3
                        </td>
                        <td class="px-6 py-4">
                            $250.00
                        </td>
                        <td class="col py-4">
                            $750.00
                        </td>
                        <td class="col py-4">
                            $75.00
                        </td>
                        <td class="px-6 py-4">
                            1
                        </td>
                        <td class="px-6 py-4">
                            2
                        </td>
                        <td class="px-6 py-4">
                            3
                        </td>
                        <td class="px-6 py-4">
                            4
                        </td>
                        <td class="px-6 py-4">
                            5
                        </td>
                        <td class="px-6 py-4">
                            6
                        </td>
                        <td class="px-6 py-4">
                            20/02/2023
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex">
                                <button type="button"
                                    class="text-green-700 hover:text-white border border-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-3.5 py-1.5 text-center me-2 mb-2 dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-600 dark:focus:ring-green-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                        class="w-5 h-5">
                                        <path
                                            d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z" />
                                        <path
                                            d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z" />
                                    </svg>

                                </button>
                                <button type="button"
                                    class="text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-3.5 py-1.5 text-center me-2 mb-2 dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                        class="w-5 h-5">
                                        <path fill-rule="evenodd"
                                            d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z"
                                            clip-rule="evenodd" />
                                    </svg>

                                </button>
                            </div>

                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal --}}
    <div id="large-modal" tabindex="-1"
        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-4xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                        AÑADIR ARTICULO A LA LISTA
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-hide="large-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5 space-y-4">
                    <form class="p-4 md:p-5">
                        <div class="grid gap-2 mb-3 grid-cols-3">
                            <div class="col-span-3">
                                <form class="max-w-md mx-auto">
                                    <label for="default-search"
                                        class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Buscar</label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 20 20">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                            </svg>
                                        </div>
                                        <input type="search" id="default-search"
                                            class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Buscar código, nombre" required />
                                        <button type="submit"
                                            class="text-white absolute end-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Buscar</button>
                                    </div>
                                </form>

                            </div>
                            <div class="col-span-1">
                                <label for="codigo"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Código</label>
                                <input type="text" id="disabled-input" aria-label="disabled input"
                                    class="mb-5 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    value="Código" disabled>
                            </div>
                            <div class="col-span-2">
                                <label for="name"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre del
                                    producto</label>
                                <input type="text" id="disabled-input" aria-label="disabled input"
                                    class="mb-5 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    value="Nombre" disabled>
                            </div>
                            <div class="col-span-1">
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
                            <div class="col-span-1">
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
                            <div class="col-span-1">
                                <label for="fecha"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fecha de
                                    ultima compra</label>
                                <input type="text" id="disabled-input" aria-label="disabled input"
                                    class="mb-5 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    value="Fecha" disabled>
                            </div>
                            {{-- Tabla de existencias --}}
                            <div class="col-span-3">
                                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                                    <table
                                        class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                        <thead
                                            class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-center">
                                                    ALMACÉN
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-center">
                                                    BAR
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-center">
                                                    BARRA
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-center">
                                                    CADDIE BAR
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-center">
                                                    CAFETERÍA
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-center">
                                                    COCINA
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                <th scope="row" class="px-6 py-4 text-center">
                                                    1
                                                </th>
                                                <td class="px-6 py-4 text-center">
                                                    2
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    3
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    4
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    5
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    6
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-span-1">
                                <label for="cantidad"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cantidad</label>
                                <input type="number" name="cantidad" id="cantidad"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="Cantidad" required="">
                            </div>
                            <div class="col-span-1">
                                <label for="costo"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Costo
                                    Unitario</label>
                                <input type="number" name="costo" id="costo"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="Precio" required="">
                            </div>
                            <div class="col-span-1">
                                <label for="unidad"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">¿Aplica
                                    IVA?</label>
                                <select id="iva"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                    <option selected="">Seleccionar</option>
                                    <option value="TV">Si</option>
                                    <option value="PC">No</option>
                                </select>
                                <div class="col-span-1">
                                    <label for="cantidad"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">%</label>
                                    <input type="number" name="cantidad" id="cantidad"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                        placeholder="Cantidad" required="">
                                </div>
                            </div>
                            <div class="inline-flex">
                                <label for="importe"
                                    class="flex items-center mb-2 text-sm font-medium text-gray-900 dark:text-white">Importe:</label>
                                <input type="number" id="disabled-input" aria-label="disabled input"
                                    class="ms-2 mb-3 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    value="Importe" disabled>
                                <label for="porcentaje"
                                    class="ms-2 flex items-center mb-2 text-sm font-medium text-gray-900 dark:text-white">IVA:</label>
                                <input type="number" id="disabled-input" aria-label="disabled input"
                                    class="ms-2 mb-3 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    value="Importe" disabled>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Modal footer -->
                <div
                    class="flex items-center p-4 md:p-5 space-x-3 rtl:space-x-reverse border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button data-modal-hide="large-modal" type="button"
                        class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Aceptar
                    </button>
                    <button data-modal-hide="large-modal" type="button"
                        class="py-2.5 px-5 ms-3 text-sm font-medium text-red-900 focus:outline-none bg-red rounded-lg border border-red-200 hover:bg-red-100 hover:text-red-700 focus:z-10 focus:ring-4 focus:ring-red-100 dark:focus:ring-red-700 dark:bg-red-800 dark:text-red-400 dark:border-red-600 dark:hover:text-white dark:hover:bg-red-700">Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>