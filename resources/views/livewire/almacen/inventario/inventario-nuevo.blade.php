<div class="ms-3 mx-3">
    <div class="flex justify-between items-end">
        <div class="flex gap-2 items-end">
            {{-- BODEGA --}}
            <form class="w-fit">
                <label for="bodega" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Bodega</label>
                <select id="bodega"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option selected>SELECCIONAR BODEGA</option>
                    <option value="US">ALMACÉN</option>
                    <option value="CA">CAFETERIA</option>
                    <option value="FR">CADDIE BAR</option>
                    <option value="DE">RESTAURANT</option>
                </select>
            </form>
            {{-- FECHA --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Fecha Existencias</label>
                <input type="text" id="fecha" aria-label="disabled input"
                    class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    value="Fecha" disabled>
            </div>
            {{-- HORA --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Hora Existencias</label>
                <input type="text" id="hora" aria-label="disabled input"
                    class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    value="Hora" disabled>
            </div>
        </div>

        <div class="flex gap-2 items-end">
            {{-- INVENTARIO TEORICO --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Inventario Teórico</label>
                <input type="text" id="inv-teorico" aria-label="disabled input"
                    class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    value="$ 0.00" disabled>
            </div>
            {{-- INVENTARIO FISICO --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Inventario Físico</label>
                <input type="text" id="inv-fisico" aria-label="disabled input"
                    class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    value="$ 0.00" disabled>
            </div>
            {{-- diferencia --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Diferencia</label>
                <input type="text" id="diferencia" aria-label="disabled input"
                    class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    value="$ 0.00" disabled>
            </div>
        </div>
    </div>

    {{-- TABLA DE INVENTARIOS --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg my-2">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        #
                    </th>
                    <th scope="col" class="px-6 py-3">
                        PRESENTACIÓN (INSUMO)
                    </th>
                    <th scope="col" class="px-6 py-3">
                        C. CON IVA
                    </th>
                    <th scope="col" class="px-6 py-3">
                        EXISTENCIA TEÓRICA
                    </th>
                    <th scope="col" class="px-6 py-3">
                        EXISTENCIA REAL
                    </th>
                    <th scope="col" class="px-6 py-3">
                        DIFERENCIA
                    </th>
                    <th scope="col" class="px-6 py-3">
                        UNIDAD INSUMO
                    </th>
                    <th scope="col" class="px-6 py-3">
                        DIF. IMPORTE
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                    <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        10101
                    </th>
                    <td class="px-6 py-2 w-96">
                        GARRAFÓN AGUA CIEL
                    </td>
                    <td class="px-6 py-2">
                        $ 20.00
                    </td>
                    <td class="px-6 py-2">
                        10
                    </td>
                    <td class="px-6 py-2">
                        12
                    </td>
                    <td class="px-6 py-2">
                        2
                    </td>
                    <td class="px-6 py-2">
                        PZ
                    </td>
                    <td class="px-6 py-2">
                        $ 40.00
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- BOTON DE CANCELAR --}}
    <button
        class="my-2 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800
        ">
        <svg class="w-5 h-5 me-2 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
            height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M6 18 17.94 6M18 18 6.06 6" />
        </svg>
        Cancelar
    </button>
    {{-- BOTON DE GUARDAR --}}
    <button
        class="my-2 text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800
        ">
        <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
            fill="currentColor" viewBox="0 0 24 24">
            <path fill-rule="evenodd"
                d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7.414A2 2 0 0 0 20.414 6L18 3.586A2 2 0 0 0 16.586 3H5Zm10 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM8 7V5h8v2a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1Z"
                clip-rule="evenodd" />
        </svg>
        Guardar
    </button>

    {{-- MODAL PARA AGREGAR PRESENTACION --}}
    
</div>
