 <x-app-layout>
     {{-- Sub barra de navegacion --}}
     <x-slot name="header">
         @include('recepcion.nav')
     </x-slot>
     {{-- $codigopv, proviene del controlador --}}
     <livewire:recepcion.ventas.nueva.container :codigopv="$codigopv->clave" />
 </x-app-layout>
