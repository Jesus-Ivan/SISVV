 <x-app-layout>
     {{-- Sub barra de navegacion --}}
     <x-slot name="header">
         @include('recepcion.nav')
     </x-slot>
     <livewire:recepcion.ventas.nueva.container>
 </x-app-layout>
