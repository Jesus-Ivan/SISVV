<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Home') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                @if (session('error_permisos'))
                <div class="bg-red-100 border border-red-400 text-red-700">
                    <span class="block sm:inline m-3">{{ session('error_permisos') }}</span>
                </div>
                @else
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        {{ __("You're logged in!") }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
