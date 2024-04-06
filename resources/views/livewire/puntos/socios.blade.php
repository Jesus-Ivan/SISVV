<div class="space-y-4">
    <div class="flex gap-4">
        <!--Search bar-->
        <div class="w-full">
            <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search
                bar</label>
            <div class="relative">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                    </svg>
                </div>
                <input type="search" id="default-search"
                    class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Nombre del socio" required />
            </div>
        </div>
        <!--USER CARD-->
        <a href="#" 
            class=" flex gap-4 w-full p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
            <!--USER IMAGE-->
            <img class="w-48 h-48 rounded-full" src="https://placehold.co/400" alt="Rounded avatar">
            <!--USER INFO-->
            <div>
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">LUCRACIO MARTINEZ SMITH</h5>
                <p class="font-normal text-gray-700 dark:text-gray-400">Activa</p>
                <p class="font-normal text-gray-700 dark:text-gray-400">7560</p>
            </div>
        </a>
    </div>
    <!-- TABLA miembros del socio -->
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3 w-full">
                        NOMBRE
                    </th>
                    <th scope="col" class="px-6 py-3 max-w-fit">
                        PARENTESCO
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                    <td scope="row" class="w-full px-6 py-4">
                        <div class="flex items-center gap-4">
                            <!-- IMAGEN DE PERFIL -->
                            <div>
                                <img class="w-20 h-20 rounded-full" src="https://placehold.co/400" alt="Rounded avatar">
                            </div>
                            <!-- INFO -->
                            <div class="dark:text-white">
                                <div class="font-medium">JUANITO MASAFECIO HERNESTINO DE LA CRUZ</div>
                            </div>
                        </div>
                    </td>
                    <td class="min-w-72 px-6 py-4">
                        Hijo(a)
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
