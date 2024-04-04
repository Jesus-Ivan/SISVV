<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('puntos.nav')
    </x-slot>
    {{-- Contenido --}}
    <div>
        <!-- Title -->
        <h4 class="text-2xl font-bold dark:text-white mx-2">Venta {{ $folioventa }} - {{ $codigopv }}</h4>
        <!-- Informacion de la venta -->
        <div>
            <!--Info del socio-->
            <div class="m-3">
                <p>Nombre: </p>
                <p>No. de socio: </p>
            </div>
            <!--Linea -->
            <hr class="h-px my-2 bg-gray-300 border-0 dark:bg-gray-700">
            <!--Tabla de articulos-->
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                DESCRIPCION
                            </th>
                            <th scope="col" class="px-6 py-3">
                                OBSERVACIONES
                            </th>
                            <th scope="col" class="px-6 py-3">
                                PRECIO
                            </th>
                            <th scope="col" class="px-6 py-3">
                                CANTIDAD
                            </th>
                            <th scope="col" class="px-6 py-3">
                                SUBTOTAL
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Huevos al gusto
                            </th>
                            <td class="px-6 py-4">
                                aoisdaoisdjoias
                            </td>
                            <td class="px-6 py-4">
                                $125
                            </td>
                            <td class="px-6 py-4">
                                1
                            </td>
                            <td class="px-6 py-4">
                                $125
                            </td>
                        </tr>

                    </tbody>
                    <tfoot>
                        <tr class="font-semibold text-gray-900 dark:text-white">
                            <th scope="row" class="px-6 py-3 text-base">Total</th>
                            <td class="px-6 py-3"></td>
                            <td class="px-6 py-3"></td>
                            <td class="px-6 py-3"></td>
                            <td class="px-6 py-3">$125</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <!--Linea -->
            <hr class="h-px my-4 bg-gray-300 border-0 dark:bg-gray-700">
            <!--Metodo de pagos -->
            <div>
                <!--Titulo de metodo de pago-->
                <h5 class="my-2 text-xl font-bold dark:text-white">Metodo de pago: efectivo</h5>
            </div>
            <!--Tabla de metodos de pagos -->
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                NO.SOCIO
                            </th>
                            <th scope="col" class="px-6 py-3">
                                METODO DE PAGO
                            </th>
                            <th scope="col" class="px-6 py-3">
                                PROPINA
                            </th>
                            <th scope="col" class="px-6 py-3">
                                SUBTOTAL
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                1234
                            </th>
                            <td class="px-6 py-4">
                                Efectivo
                            </td>
                            <td class="px-6 py-4">
                                $0
                            </td>
                            <td class="px-6 py-4">
                                $125
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="font-semibold text-gray-900 dark:text-white">
                            <th scope="row" class="px-6 py-3 text-base">
                                <p>Descuento</p>
                                <p>Total</p>
                            </th>
                            <td class="px-6 py-3"></td>
                            <td class="px-6 py-3"></td>
                            <td class="px-6 py-3">
                                <p>$0</p>
                                <p>$125</p>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <!--Botones de navegacion (cancelar, guardar, cerrar venta)-->
            <div class="m-2">
                <button type="button"
                    class="inline-flex items-center focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">
                    Regresar
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
