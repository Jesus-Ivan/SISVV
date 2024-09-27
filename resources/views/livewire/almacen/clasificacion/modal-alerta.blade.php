<div>
    <!--Modal de eliminacion -->
    <x-modal title="Ingresar Clasificación" name="alert">
        <x-slot name='body'>
            <div class="text-center">
                <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <h3 class="mb-5 text-xl font-normal text-gray-500 dark:text-gray-400">
                    Se han encontrado registros similares:
                    <ul>
                        @foreach ($listaClasificacion as $similarClasificacion)
                            <li>{{ $similarClasificacion->nombre }}</li>
                        @endforeach
                    </ul>
                </h3>
                <p class="text-gray-500 dark:text-gray-400">
                    ¿Deseas continuar con el registro?
                </p>
            </div>
        </x-slot>
        <x-slot name='footer'>
            <button type="button" wire:click='confirmSave()'
                class="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">
                Ingresar
            </button>
        </x-slot>
    </x-modal>
</div>
