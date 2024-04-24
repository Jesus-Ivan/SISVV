<div>
    {{-- Buscar por fecha --}}
    <div class="relative ms-3 w-40">
        <label for="name" class="block mb-2 text-base font-medium text-gray-900 dark:text-white">Buscar por
            día:</label>
        <input type="date" id="fecha"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
    </div>

    {{-- Tabla --}}
    <div class="ms-3 mx-3 my-3">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            FOLIO
                        </th>
                        <th scope="col" class="px-6 py-3">
                            FECHA DE SALIDA
                        </th>
                        <th scope="col" class="px-6 py-3">
                            CANTIDAD
                        </th>
                        <th scope="col" class="px-4 py-3">
                            ORIGEN
                        </th>
                        <th scope="col" class="px-6 py-3">
                            DESTINO
                        </th>
                        <th scope="col" class="px-6 py-3">
                            NOTA
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
                            12/12/2023
                        </td>
                        <td class="px-6 py-4">
                            5 ARTÍCULOS
                        </td>
                        <td class="px-6 py-4">
                            ALMACÉN
                        </td>
                        <td class="px-6 py-4">
                            CAMPO
                        </td>
                        <td class="px-6 py-4">
                            AUTORIZADO POR EL LIC. RAY
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex">
                                <button type="button"
                                    class="text-gray-700 hover:text-white border border-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-3.5 py-1.5 text-center me-2 mb-2 dark:border-gray-500 dark:text-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                        class="w-5 h-5">
                                        <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                                        <path fill-rule="evenodd"
                                            d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z"
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
</div>
