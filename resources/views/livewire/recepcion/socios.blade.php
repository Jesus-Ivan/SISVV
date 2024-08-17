<div>
    <div class="flex gap-2  items-center ms-3 mx-3">
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
                    placeholder="Nombre o numero de socio"/>
            </div>
        </div>
        <!--Loading indicator-->
        <div wire:loading>
            @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
        </div>
    </div>

    <div class="ms-3 mx-3 my-3">
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
                        <th scope="col" class="px-6 py-3 min-w-40 text-center">
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
                                        <div class="font-medium uppercase">
                                            {{ $socio->nombre . ' ' . $socio->apellido_p . ' ' . $socio->apellido_m }}
                                        </div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $socio->id }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $socio->tel_1 }}</p>
                                    </div>
                                </div>
                            </td>
                            {{-- TIPO MEMBRESIA --}}
                            <td class="min-w-60 px-6 py-4">
                                {{ $socio->descripcion }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('recepcion.socios.editar', $socio->id) }}">
                                    <button type="button"
                                        class="text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                            class="w-5 h-5">
                                            <path fill-rule="evenodd"
                                                d="M11.32 6.176H5c-1.105 0-2 .949-2 2.118v10.588C3 20.052 3.895 21 5 21h11c1.105 0 2-.948 2-2.118v-7.75l-3.914 4.144A2.46 2.46 0 0 1 12.81 16l-2.681.568c-1.75.37-3.292-1.263-2.942-3.115l.536-2.839c.097-.512.335-.983.684-1.352l2.914-3.086Z"
                                                clip-rule="evenodd" />
                                            <path fill-rule="evenodd"
                                                d="M19.846 4.318a2.148 2.148 0 0 0-.437-.692 2.014 2.014 0 0 0-.654-.463 1.92 1.92 0 0 0-1.544 0 2.014 2.014 0 0 0-.654.463l-.546.578 2.852 3.02.546-.579a2.14 2.14 0 0 0 .437-.692 2.244 2.244 0 0 0 0-1.635ZM17.45 8.721 14.597 5.7 9.82 10.76a.54.54 0 0 0-.137.27l-.536 2.84c-.07.37.239.696.588.622l2.682-.567a.492.492 0 0 0 .255-.145l4.778-5.06Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span class="sr-only">Editar</span>
                                    </button>
                                </a>
                                <a type="button" href="{{ route('recepcion.socios.qr', $socio->id) }}" target="_blank"
                                    class="text-gray-700 border border-gray-700 hover:bg-gray-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-gray-500 dark:text-gray-500 dark:hover:text-white dark:focus:ring-gray-800 dark:hover:bg-gray-500">
                                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4h6v6H4V4Zm10 10h6v6h-6v-6Zm0-10h6v6h-6V4Zm-4 10h.01v.01H10V14Zm0 4h.01v.01H10V18Zm-3 2h.01v.01H7V20Zm0-4h.01v.01H7V16Zm-3 2h.01v.01H4V18Zm0-4h.01v.01H4V14Z" />
                                        <path stroke="currentColor" stroke-linejoin="round" stroke-width="2"
                                            d="M7 7h.01v.01H7V7Zm10 10h.01v.01H17V17Z" />
                                    </svg>
                                    <span class="sr-only">QR</span>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="block w-full justify-end">{{ $listaSocios->links() }}</div>
        </div>
    </div>

    {{-- Boton de regresar --}}
    <div class="ms-3 mx-3">
        <a type="button" href="{{ route('recepcion') }}"
            class="my-2 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
            <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 12h14M5 12l4-4m-4 4 4 4" />
            </svg>Regresar
        </a>
    </div>
</div>
