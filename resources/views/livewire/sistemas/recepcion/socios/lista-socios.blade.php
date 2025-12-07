<div class="ms-3 mx-3">
    <div class="flex gap-2 my-2 items-center">
        <!-- BARRA DE BUSQUEDA -->
        <div class="w-full max-w-sm">
            <label for="default-search"
                class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
            <div class="relative">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                    </svg>
                </div>
                <input wire:model.live.debounce.500ms="search" type="text" id="default-search"
                    class="block w-full p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Nombre o numero de socio" />
            </div>
        </div>
        <!--Loading indicator-->
        <div wire:loading>
            @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
        </div>
    </div>

    <div class="my-3">
        {{-- TABLA DE SOCIOS --}}
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3 w-full">
                            SOCIO
                        </th>
                        <th scope="col" class="px-6 py-3 min-w-60">
                            MEMBRESIA
                        </th>
                        <th scope="col" class="px-6 py-3 min-w-60">
                            ESTADO
                        </th>
                        <th scope="col" class="px-6 py-3 min-w-60 text-center">
                            ACCIONES
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($listaSocios as $socio)
                        <tr wire:key="{{ $socio->id }}"
                            class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                            <td scope="row" class="w-full px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <!-- IMAGEN DE PERFIL -->
                                    <div>
                                        <img class="w-20 h-20 rounded-full" src="{{ asset($socio->img_path) }}"
                                            alt="Rounded avatar">
                                    </div>
                                    <!-- INFO -->
                                    <div class="dark:text-white">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $socio->id }}</p>
                                        <div class="font-medium uppercase">
                                            {{ $socio->nombre . ' ' . $socio->apellido_p . ' ' . $socio->apellido_m }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            {{-- TIPO MEMBRESIA --}}
                            <td class="min-w-60 px-6 py-4">
                                {{ $socio->socioMembresia->membresia->descripcion }}
                            </td>
                            <td class="min-w-60 px-6 py-4">
                                @if ($socio->deleted_at)
                                    <p><span class="text-red-600 font-bold">ELIMINADO:</span></p>
                                    <p><span
                                            class="text-red-600 font-bold">{{ $socio->deleted_at->format('Y-m-d H:i') }}</span>
                                    </p>
                                @else
                                    <span class="text-green-600 font-bold">ACTIVO</span>
                                @endif
                            </td>
                            <td class="min-w-60 px-6 py-4 text-center">
                                @if ($socio->deleted_at)
                                    <a type="button" href="{{ $this->generarEdoCuenta($socio->id) }}" target="_blank"
                                        class="text-gray-700 border border-gray-700 hover:bg-gray-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-gray-500 dark:text-gray-500 dark:hover:text-white dark:focus:ring-gray-800 dark:hover:bg-gray-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                            class="w-5 h-5">
                                            <path fill-rule="evenodd"
                                                d="M8 3a2 2 0 0 0-2 2v3h12V5a2 2 0 0 0-2-2H8Zm-3 7a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h1v-4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v4h1a2 2 0 0 0 2-2v-5a2 2 0 0 0-2-2H5Zm4 11a1 1 0 0 1-1-1v-4h8v4a1 1 0 0 1-1 1H9Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span class="sr-only">Imprimir</span>
                                    </a>
                                    {{-- BOTÓN DE RESTAURAR (Si el socio está eliminado) --}}
                                    <button type="button" wire:click="restoreSocio({{ $socio->id }})"
                                        wire:confirm="¿Deseas reingresar al socio {{ $socio->id }}?"
                                        class="text-green-700 border border-green-700 hover:bg-green-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:focus:ring-green-800 dark:hover:bg-green-500">
                                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd"
                                                d="M9 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4H7Zm8-1a1 1 0 0 1 1-1h1v-1a1 1 0 1 1 2 0v1h1a1 1 0 1 1 0 2h-1v1a1 1 0 1 1-2 0v-1h-1a1 1 0 0 1-1-1Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span class="sr-only">Restaurar</span>
                                    </button>
                                @else
                                    {{-- BOTÓN DE ELIMINAR (Si el socio está activo) --}}
                                    <button type="button" wire:click="deleteSocio({{ $socio->id }})"
                                        wire:confirm="¿Estás seguro de que quieres eliminar al socio {{ $socio->id }}?"
                                        class="text-red-700 border border-red-700 hover:bg-red-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:focus:ring-red-800 dark:hover:bg-red-500">
                                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd"
                                                d="M8.586 2.586A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h3a1 1 0 1 1 0 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8a1 1 0 0 1 0-2h3V4a2 2 0 0 1 .586-1.414ZM10 6h4V4h-4v2Zm1 4a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Zm4 0a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span class="sr-only">Eliminar</span>
                                    </button>
                                @endif

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="block w-full justify-end">{{ $listaSocios->links() }}</div>
        </div>
    </div>
</div>
