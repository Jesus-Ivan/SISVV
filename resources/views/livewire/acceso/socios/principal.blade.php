<div class="ms-3 mx-3">
    <div class="flex gap-4">
        <a
            class=" flex gap-4 w-full p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
            <!--USER IMAGE-->
            @foreach ($result as $index => $socio)
                @if ($socio)
                    <img class="w-52 h-52 rounded-full" src="{{ asset($socio->img_path) }}" alt="Imagen de socio">
                @else
                    <img class="w-52 h-52 rounded-full" src="https://placehold.co/400" alt="Rounded avatar">
                @endif

                <!--USER INFO-->

                <div>
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                        {{ $socio ? $socio->nombre . ' ' . $socio->apellido_p . ' ' . $socio->apellido_m : '' }}
                    </h5>
                    <p class="font-normal text-gray-700 dark:text-gray-400">
                        ESTADO MEMBRESIA: {{ $socio ? $socio->socioMembresia->estado : '' }}
                    </p>
                    <p class="font-normal text-gray-700 dark:text-gray-400">
                        TIPO DE MEMBRESIA: {{ $socio ? $socio->socioMembresia->membresia->descripcion : '' }}
                    </p>
                    <p class="font-normal text-gray-700 dark:text-gray-400">
                        NO. SOCIO: {{ $socio ? $socio->id : '' }}
                    </p>

                    @if ($socio)
                        <div>
                            @if ($socio->socioMembresia->estado == 'CAN')
                                <p class="text-xl font-bold text-red-600">ACCESO DENEGADO</p>
                            @else
                                <p class="text-xl font-bold text-green-500">ACCESO PERMITIDO</p>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </a>

        <!--Search bar-->
        <form class="w-full" wire:submit='buscar'>
            <div class="relative min-w-full">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                    </svg>
                </div>
                <input type="search" id="default-search" wire:model='search'
                    class=" max-h-10 block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Número de socio" autofocus />
            </div>
        </form>
        <!--Loading indicator-->
        <div wire:loading>
            @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
        </div>
    </div>

    <!-- Linea -->
    <hr class="h-px my-3 bg-gray-300 border-0 dark:bg-gray-700">
    <h4 class="text-2xl font-bold dark:text-white mb-4">INTEGRANTES</h4>

    <!-- grid miembros del socio -->
    <div class="grid grid-cols-4 gap-4">
        @if ($socio)
            @foreach ($socio->integrantesSocio as $integrante)
                <div class="p-4 rounded-lg shadow-lg" wire:key ="{{ $integrante->id }}">
                    <!--USER IMAGE-->
                    <div class="w-full">
                        <img class="w-48 h-48 rounded-full" src="{{ asset($integrante->img_path_integrante) }}"
                            alt="Imagen de socio">
                    </div>
                    <!--USER INFO-->
                    <div>
                        <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white">
                            {{ $integrante->nombre_integrante . ' ' . $integrante->apellido_p_integrante . ' ' . $integrante->apellido_m_integrante }}
                        </h5>
                        <p class="font-normal text-gray-700 dark:text-gray-400">
                            {{ $integrante->parentesco }}
                        </p>
                    </div>
                </div>
            @endforeach
        @endif
    </div>


</div>
