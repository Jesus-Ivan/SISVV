 <x-app-layout>
     {{-- Sub barra de navegacion --}}
     <x-slot name="header">
         @include('puntos.nav')
     </x-slot>

     {{-- Contenido --}}
     <div class="flex items-center m-2">
         <button id="dropdownMenuIconButton" data-dropdown-toggle="dropdownDots"
             class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
             type="button">
             <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                 viewBox="0 0 16 3">
                 <path
                     d="M2 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm6.041 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM14 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Z" />
             </svg>
         </button>
         <!-- Dropdown menu -->
         <div id="dropdownDots"
             class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 dark:divide-gray-600">
             <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownMenuIconButton">
                 <li>
                     <a href="{{ route('pv.ventas.nueva', ['codigopv' => $codigopv]) }}"
                         class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Nueva</a>
                 </li>
                 <li>
                     <a href="{{ route('pv.ventas.reporte',['codigopv' => $codigopv]) }}"
                         class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Reportes</a>
                 </li>
             </ul>
         </div>
         <!-- Title -->
         <h4 class="text-2xl font-bold dark:text-white mx-2">Ventas-{{ $codigopv }}</h4>
     </div>
     <!--SEARCH BAR-->
     <div class="max-w-md m-2">
         <label for="default-search"
             class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search bar</label>
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
     {{-- TABLA DE VENTAS --}}
     <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
         <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
             <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                 <tr>
                     <th scope="col" class="px-6 py-3">
                         FOLIO
                     </th>
                     <th scope="col" class="px-6 py-3">
                         NO.SOCIO
                     </th>
                     <th scope="col" class="px-6 py-3">
                         NOMBRE
                     </th>
                     <th scope="col" class="px-6 py-3">
                         FECHA
                     </th>
                     <th scope="col" class="px-6 py-3">
                         ESTADO
                     </th>
                     <th scope="col" class="px-6 py-3">
                         TOTAL
                     </th>
                     <th scope="col" class="px-6 py-3">
                        ACCIONES
                     </th>
                 </tr>
             </thead>
             <tbody>
                 <tr
                     class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                     <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                         1234
                     </th>
                     <td class="px-6 py-4">
                         7567
                     </td>
                     <td class="px-6 py-4">
                         MASAFESIO PEREZ ROMERO
                     </td>
                     <td class="px-6 py-4">
                         12/12/2023
                     </td>
                     <td class="px-4 py-2">
                         <span
                             class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Cerrada
                         </span>
                     </td>
                     <td class="px-6 py-4">
                         $1000
                     </td>
                     <td class="px-6 py-4">
                         <a href="{{route('pv.ventas.ver',['codigopv'=>$codigopv, 'folioventa'=>1234])}}">
                             <button type="button"
                                 class="text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500">
                                 <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                     class="w-[24px] h-[24px]">
                                     <path stroke="currentColor" stroke-linecap="round" stroke-width="3"
                                         d="M12 6h.01M12 12h.01M12 18h.01" />
                                 </svg>
                                 <span class="sr-only">Ver</span>
                             </button>
                         </a>
                         <button type="button"
                             class="text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500">
                             <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                 class="w-6 h-6">
                                 <path fill-rule="evenodd"
                                     d="M7.875 1.5C6.839 1.5 6 2.34 6 3.375v2.99c-.426.053-.851.11-1.274.174-1.454.218-2.476 1.483-2.476 2.917v6.294a3 3 0 0 0 3 3h.27l-.155 1.705A1.875 1.875 0 0 0 7.232 22.5h9.536a1.875 1.875 0 0 0 1.867-2.045l-.155-1.705h.27a3 3 0 0 0 3-3V9.456c0-1.434-1.022-2.7-2.476-2.917A48.716 48.716 0 0 0 18 6.366V3.375c0-1.036-.84-1.875-1.875-1.875h-8.25ZM16.5 6.205v-2.83A.375.375 0 0 0 16.125 3h-8.25a.375.375 0 0 0-.375.375v2.83a49.353 49.353 0 0 1 9 0Zm-.217 8.265c.178.018.317.16.333.337l.526 5.784a.375.375 0 0 1-.374.409H7.232a.375.375 0 0 1-.374-.409l.526-5.784a.373.373 0 0 1 .333-.337 41.741 41.741 0 0 1 8.566 0Zm.967-3.97a.75.75 0 0 1 .75-.75h.008a.75.75 0 0 1 .75.75v.008a.75.75 0 0 1-.75.75H18a.75.75 0 0 1-.75-.75V10.5ZM15 9.75a.75.75 0 0 0-.75.75v.008c0 .414.336.75.75.75h.008a.75.75 0 0 0 .75-.75V10.5a.75.75 0 0 0-.75-.75H15Z"
                                     clip-rule="evenodd" />
                             </svg>
                             <span class="sr-only">Imprimir</span>
                         </button>
                     </td>
                 </tr>
                 <tr
                     class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                     <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                         1234
                     </th>
                     <td class="px-6 py-4">
                         7567
                     </td>
                     <td class="px-6 py-4">
                         MASAFESIO PEREZ ROMERO
                     </td>
                     <td class="px-6 py-4">
                         12/12/2023
                     </td>
                     <td class="px-4 py-2">
                         <span
                             class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">Abierta
                         </span>
                     </td>
                     <td class="px-6 py-4">
                         $1000
                     </td>
                     <td class="px-6 py-4">
                         <a href="{{route('pv.ventas.editar',['codigopv'=>$codigopv, 'folioventa'=>1234])}}">
                             <button type="button"
                                 class="text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500">
                                 <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                     class="w-[24px] h-[24px]">
                                     <path stroke="currentColor" stroke-linecap="round" stroke-width="3"
                                         d="M12 6h.01M12 12h.01M12 18h.01" />
                                 </svg>
                                 <span class="sr-only">Ver</span>
                             </button>
                         </a>
                         <button type="button"
                             class="text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500">
                             <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                 class="w-6 h-6">
                                 <path fill-rule="evenodd"
                                     d="M7.875 1.5C6.839 1.5 6 2.34 6 3.375v2.99c-.426.053-.851.11-1.274.174-1.454.218-2.476 1.483-2.476 2.917v6.294a3 3 0 0 0 3 3h.27l-.155 1.705A1.875 1.875 0 0 0 7.232 22.5h9.536a1.875 1.875 0 0 0 1.867-2.045l-.155-1.705h.27a3 3 0 0 0 3-3V9.456c0-1.434-1.022-2.7-2.476-2.917A48.716 48.716 0 0 0 18 6.366V3.375c0-1.036-.84-1.875-1.875-1.875h-8.25ZM16.5 6.205v-2.83A.375.375 0 0 0 16.125 3h-8.25a.375.375 0 0 0-.375.375v2.83a49.353 49.353 0 0 1 9 0Zm-.217 8.265c.178.018.317.16.333.337l.526 5.784a.375.375 0 0 1-.374.409H7.232a.375.375 0 0 1-.374-.409l.526-5.784a.373.373 0 0 1 .333-.337 41.741 41.741 0 0 1 8.566 0Zm.967-3.97a.75.75 0 0 1 .75-.75h.008a.75.75 0 0 1 .75.75v.008a.75.75 0 0 1-.75.75H18a.75.75 0 0 1-.75-.75V10.5ZM15 9.75a.75.75 0 0 0-.75.75v.008c0 .414.336.75.75.75h.008a.75.75 0 0 0 .75-.75V10.5a.75.75 0 0 0-.75-.75H15Z"
                                     clip-rule="evenodd" />
                             </svg>
                             <span class="sr-only">Imprimir</span>
                         </button>
                     </td>
                 </tr>
             </tbody>
         </table>
     </div>
 </x-app-layout>
