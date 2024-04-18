<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('recepcion.nav')
    </x-slot>

    {{-- Contenido --}}
    <div >
        <!-- Title -->
        <h4 class="text-2xl font-bold dark:text-white mx-2">Editar socio - {{$socio->id}}</h4>
        <div>
           <livewire:recepcion.socios-editar :socio="$socio"/>
        </div>
    </div>
</x-app-layout>
