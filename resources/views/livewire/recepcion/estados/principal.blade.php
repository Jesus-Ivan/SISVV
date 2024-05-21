<div>
    <!-- BARRA DE BUSQUEDA -->
    <div class=" m-2 flex items-end gap-4">
        <div class="flex items-end gap-4 grow">
            {{-- Fecha Inicio --}}
            <div class="w-72">
                <label for="inicio" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Inicio</label>
                <input type="date" id="inicio" wire:model.live.debounce.500ms="fechaInicio"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            </div>
            {{-- Fecha fin --}}
            <div class="w-72">
                <label for="fin" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fin</label>
                <input type="date" id="fin" wire:model.live.debounce.500ms="fechaFin"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            </div>
            <!-- INPUT -->
            <div>
                <div class="relative w-72 max-h-12">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                    <input type="search" id="default-search" wire:model.live.debounce.500ms='search'
                        class=" max-h-10 block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Nombre o numero de socio" />
                </div>
            </div>
            <!--Loading indicator-->
            <div wire:loading>
                @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
            </div>
        </div>
    </div>
    <!--RADIO BUTTONS-->
    <div class="m-2">
        <label for="radio-buttons"
            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Conceptos</label>
        <div class="flex gap-8 m-2" id="radio-buttons">
            <div class="flex items-center">
                <input id="T-radio" type="radio" value="T" name="todos"
                    wire:model.live.debounce.500ms='radioButon'
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                <label for="T-radio" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Mostrar
                    todos</label>
            </div>
            <div class="flex items-center-2">
                <input id="P-radio" type="radio" value="P" name="pendientes"
                    wire:model.live.debounce.500ms='radioButon'
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                <label for="P-radio" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Mostrar
                    pendientes</label>
            </div>
            <div class="flex items-center">
                <input id="C-radio" type="radio" value="C" name="consumos"
                    wire:model.live.debounce.500ms='radioButon'
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                <label for="C-radio" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Mostrar
                    consumos</label>
            </div>
        </div>
    </div>
    {{-- TABLA DE SOCIOS --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        SOCIO
                    </th>
                    <th scope="col" class="px-6 py-3">
                        MEMBRESIA
                    </th>
                    <th scope="col" class="px-6 py-3">
                        ACCIONES
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->resultSocios as $index => $socio)
                    <tr wire:key={{ $index }}
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                        <td scope="row" class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <!-- IMAGEN DE PERFIL -->
                                <div>
                                    <img class="w-20 h-20 rounded-full" src="{{ asset($socio->img_path) }}"
                                        alt="Rounded avatar">
                                </div>
                                <!-- INFO -->
                                <div class="dark:text-white">
                                    <div class="font-medium">{{ $socio->nombre }}</div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No.Socio:
                                        {{ $socio->id }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Correo:
                                        {{ $socio->correo ? $socio->correo : 'N/R' }}</p>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4 ">
                            <div>
                                {{ $socio->socioMembresia->membresia->descripcion }}
                            </div>
                            {{ $socio->socioMembresia->estado }}
                        </td> 
                        <td class="px-6 py-4">
                            <a
                                href="{{ route('recepcion.estado.nuevo',['socio' => $socio->id]) }}">
                                <button type="button"
                                    class="text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                        class="w-[24px] h-[24px]">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-width="3"
                                            d="M12 6h.01M12 12h.01M12 18h.01" />
                                    </svg>
                                    <span class="sr-only">Ver</span>
                                </button>
                            </a>
                            {{-- <button type="button"
                                class="text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="w-6 h-6">
                                    <path
                                        d="M2.038 5.61A2.01 2.01 0 0 0 2 6v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6c0-.12-.01-.238-.03-.352l-.866.65-7.89 6.032a2 2 0 0 1-2.429 0L2.884 6.288l-.846-.677Z" />
                                    <path
                                        d="M20.677 4.117A1.996 1.996 0 0 0 20 4H4c-.225 0-.44.037-.642.105l.758.607L12 10.742 19.9 4.7l.777-.583Z" />
                                </svg>
                                <span class="sr-only">Editar</span>
                            </button> --}}
                            <a type="button"
                                href="{{ route('recepcion.estado.reporte', ['socio' => $socio->id, 'tipo' => $radioButon, 'fInicio' => $fechaInicio, 'fFin' => $fechaFin]) }}"
                                target="_blank"
                                class="text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="w-6 h-6">
                                    <path fill-rule="evenodd"
                                        d="M8 3a2 2 0 0 0-2 2v3h12V5a2 2 0 0 0-2-2H8Zm-3 7a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h1v-4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v4h1a2 2 0 0 0 2-2v-5a2 2 0 0 0-2-2H5Zm4 11a1 1 0 0 1-1-1v-4h8v4a1 1 0 0 1-1 1H9Z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="sr-only">Imprimir</span>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div>
            {{ $this->resultSocios->links() }}
        </div>
    </div>
</div>
