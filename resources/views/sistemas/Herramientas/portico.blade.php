<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('sistemas.nav')
    </x-slot>

    {{-- Contenido --}}
    <div class="flex items-center m-2">
        <h4 class="text-2xl font-bold dark:text-white mx-2">Sincronización PORTICO</h4>
    </div>

    <div class="ms-3 mx-3 max-w-2xl">
        <form action="{{ route('sistemas.portico.sync') }}" method="POST">
            @csrf
            <button type="submit"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none">
                Sincronizar ahora
            </button>
        </form>

        {{-- Mensajes --}}
        @if (session('success'))
            <div class="mt-4 p-3 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400">
                {{ session('success') }}
            </div>
        @endif
        @if (session('fail'))
            <div class="mt-4 p-3 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400">
                {{ session('fail') }}
            </div>
        @endif
    </div>
</x-app-layout>
