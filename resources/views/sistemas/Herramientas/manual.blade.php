<x-app-layout>
    {{-- Sub barra de navegacion --}}
    <x-slot name="header">
        @include('sistemas.nav')
    </x-slot>

    {{-- Contenido --}}
    <div class="flex items-center m-2">
        <h4 class="text-2xl font-bold dark:text-white mx-2">Manual de Usuario</h4>
    </div>

    <div class="ms-3 mx-3 max-w-2xl">
        @if ($existe)
            <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">
                Manual actual: <strong>{{ $nombre }}</strong>. Se reemplazará al subir uno nuevo.
            </p>
        @else
            <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">
                Todavía no hay manual subido. Sube un PDF y la app lo mostrará tras sincronizar.
            </p>
        @endif

        <form action="{{ route('sistemas.manual') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="pdf" accept="application/pdf" required
                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 dark:bg-gray-800 dark:border-gray-600 focus:outline-none">
            @error('pdf')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <button type="submit"
                class="mt-4 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none">
                Subir / Reemplazar manual
            </button>
        </form>

        {{-- Mensajes --}}
        @if (session('success'))
            <div class="mt-4 p-3 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400">
                {{ session('success') }}
            </div>
        @endif
    </div>
</x-app-layout>
