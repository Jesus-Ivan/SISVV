<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('almacen.nav')
    </x-slot>

    {{-- Contenido --}}
    <div class="py-5">
        <div class="flex ms-2">
            <div class="inline-flex flex-grow">
                <h4 class="flex items-center ms-2 text-2xl font-bold dark:text-white">Editar receta</h4>
            </div>
        </div>
    </div>

    <div>
        <livewire:almacen.recetas.recetas-editar/> 
    </div>
    
</x-app-layout>
