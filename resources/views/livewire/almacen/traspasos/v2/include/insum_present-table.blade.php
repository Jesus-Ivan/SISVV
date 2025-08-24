<div class="relative overflow-x-auto shadow-md sm:rounded-lg my-2">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    #
                </th>
                <th scope="col" class="px-6 py-3">
                    INSUMO
                </th>
                <th scope="col" class="px-6 py-3">
                    CANTIDAD
                </th>
                <th scope="col" class="px-6 py-3">
                    UNIDAD
                </th>
                <th scope="col" class="px-6 py-3">
                    COSTO U.
                </th>
                <th scope="col" class="px-6 py-3">
                    IVA
                </th>
                <th scope="col" class="px-6 py-3">
                    C. C. IMPUESTO
                </th>
                <th scope="col" class="px-6 py-3">
                    IMPORTE
                </th>
                <th scope="col" class="px-6 py-3">

                </th>
            </tr>
        </thead>
        <tbody>
            <tr
                class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    10
                </th>
                <td class="px-6 py-2 w-80">
                    AGUA GARRAFON
                </td>
                <td class="px-6 py-2">
                    <button type="button" x-data x-on:click="$dispatch('open-modal', {name:'modal-detalles'})"
                        class="text-black bg-gray-300 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-2 py-2 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                        800.500
                        <svg class="rtl:rotate-180 w-4.5 h-4.5 ms-3" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                            viewBox="0 0 24 24">
                            <path fill-rule="evenodd"
                                d="M11.32 6.176H5c-1.105 0-2 .949-2 2.118v10.588C3 20.052 3.895 21 5 21h11c1.105 0 2-.948 2-2.118v-7.75l-3.914 4.144A2.46 2.46 0 0 1 12.81 16l-2.681.568c-1.75.37-3.292-1.263-2.942-3.115l.536-2.839c.097-.512.335-.983.684-1.352l2.914-3.086Z"
                                clip-rule="evenodd" />
                            <path fill-rule="evenodd"
                                d="M19.846 4.318a2.148 2.148 0 0 0-.437-.692 2.014 2.014 0 0 0-.654-.463 1.92 1.92 0 0 0-1.544 0 2.014 2.014 0 0 0-.654.463l-.546.578 2.852 3.02.546-.579a2.14 2.14 0 0 0 .437-.692 2.244 2.244 0 0 0 0-1.635ZM17.45 8.721 14.597 5.7 9.82 10.76a.54.54 0 0 0-.137.27l-.536 2.84c-.07.37.239.696.588.622l2.682-.567a.492.492 0 0 0 .255-.145l4.778-5.06Z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </td>
                <td class="px-6 py-2">
                    LT
                </td>
                <td class="px-6 py-2">
                    $14.50
                </td>
                <td class="px-6 py-2">
                    16 %
                </td>
                <td class="px-6 py-2">
                    $16.62
                </td>
                <td class="px-6 py-2">
                    $50.46
                </td>
                <td class="px-6 py-2 text-center">
                    <button type="button"
                        class="text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-3.5 py-2 text-center me-2 mb-2 dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                            <path fill-rule="evenodd"
                                d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>

    {{-- MODAL DE ARTICULOS --}}
    <x-modal name="modal-detalles" title="ESPECIFICAR PRESENTACIÓN">
        <x-slot name='body'>
            <!-- Modal content -->
            <div class="h-auto max-w-4xl overflow-y-auto">
                <!-- Modal body -->
                <div class="p-1 w-full max-w-2xl max-h-full">
                    <!-- Result table-->
                    <div class="relative overflow-x-auto h-80 shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        #
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        PRESENTACIÓN
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        CANTIDAD
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        RENDIMIENTO
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        CANT. INSUMO
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                    <th scope="row"
                                        class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        1234
                                    </th>
                                    <td class="px-6 py-2 w-80">
                                        GARRAFON BONAFONT 20L
                                    </td>
                                    <td class="px-6 py-2">
                                        <input type="number"
                                            class="max-w-20 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block  p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="0.000" />
                                    </td>
                                    <td class="px-6 py-2">
                                        20
                                    </td>
                                    <td class="px-6 py-2">
                                        0.000
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center mt-3 border-gray-200 rounded-b dark:border-gray-600">
                    <div class="flex grow">
                        <button type="button"
                            class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 my-2 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Aceptar
                        </button>
                    </div>
                    <p>CANTIDAD INSUMO: 20.000</p>
                </div>
        </x-slot>
    </x-modal>
</div>
