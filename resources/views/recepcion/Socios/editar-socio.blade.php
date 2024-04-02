<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('recepcion.nav')
    </x-slot>

    {{-- Contenido --}}
    <div >
        <!-- Title -->
        <h4 class="text-2xl font-bold dark:text-white mx-2">Editar socio - {{$socio}}</h4>
        <div>
           <livewire:recepcion.socios-editar/>
        </div>
    </div>
</x-app-layout>
