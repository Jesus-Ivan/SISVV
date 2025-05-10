<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('acceso.nav')
    </x-slot>
    <h4 class="flex items-center ms-3 mx-3 text-2xl font-bold dark:text-white">SOCIOS VISTA VERDE</h4>
    <livewire:acceso.socios.principal/>
</x-app-layout>