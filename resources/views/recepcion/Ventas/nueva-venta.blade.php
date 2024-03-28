 <x-app-layout>
     {{-- Sub barra de navegacion --}}
     <x-slot name="header">
         @include('recepcion.nav')
     </x-slot>

     {{-- Contenido --}}
     <div class="flex items-center m-2">
         <!-- Title -->
         <h4 class="text-2xl font-bold dark:text-white mx-2">Nueva venta-Recepcion</h4>
     </div>
 </x-app-layout>
