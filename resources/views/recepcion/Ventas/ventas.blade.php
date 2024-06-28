 <x-app-layout>
     {{-- Sub barra de navegacion --}}
     <x-slot name="header">
         @include('recepcion.nav')
     </x-slot>

     {{-- Contenido --}}
     <div>
         <!--Boton de acciones de ventas-->
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
                         <a href="{{ route('recepcion.ventas.nueva') }}"
                             class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Nueva</a>
                     </li>
                     <li>
                         <a href="{{ route('recepcion.ventas.reporte') }}"
                             class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Reportes</a>
                     </li>
                 </ul>
             </div>
             <!-- Title -->
             <h4 class="text-2xl font-bold dark:text-white mx-2">Ventas-Recepcion</h4>
         </div>
         {{-- Componente de la tabla con search bar --}}
         {{-- $codigopv, proviene del controlador --}}
         <livewire:puntos.ventas.principal :codigopv="$codigopv->clave" />
     </div>
 </x-app-layout>
