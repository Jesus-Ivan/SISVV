<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('sistemas.nav')
    </x-slot>

    {{-- Contenido --}}
    <form action="{{ route('subirSocios') }}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file">
        <br>
        <input type="submit" value="IMPORTAR">
    </form>
</x-app-layout>