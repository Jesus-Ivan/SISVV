<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('sistemas.nav')
    </x-slot>
    <h4 class="flex items-center ms-3 mx-3 my-2 text-2xl font-bold dark:text-white">SOCIOS</h4>

    <div>
        <livewire:sistemas.recepcion.lista-socios />
    </div>
</x-app-layout>