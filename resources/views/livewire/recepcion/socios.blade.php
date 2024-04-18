<div>
    <!-- BARRA DE BUSQUEDA -->
    <div class="max-w-sm m-2">
        <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
        <div class="relative">
            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                </svg>
            </div>
            <input wire:model.live.debounce.500ms="search" type="text" id="default-search" 
                class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Nombre o numero de socio" value="" />
        </div>
    </div>
    {{-- TABLA DE SOCIOS --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3 w-full">
                        SOCIO
                    </th>
                    <th scope="col" class="px-6 py-3 min-w-72">
                        MEMBRESIA
                    </th>
                    <th scope="col" class="px-6 py-3 max-w-fit">
                        ACCIONES
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($listaSocios as $socio)
                    <tr id="{{$socio->id}}"
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                        <td scope="row" class="w-full px-6 py-4">
                            <div class="flex items-center gap-4">
                                <!-- IMAGEN DE PERFIL -->
                                <div>
                                    <img class="w-20 h-20 rounded-full" src="{{asset($socio->img_path)}}"
                                        alt="Rounded avatar">
                                </div>
                                <!-- INFO -->
                                <div class="dark:text-white">
                                    <div class="font-medium">{{$socio->nombre}}</div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{$socio->id}}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{$socio->tel_celular}}</p>
                                </div>
                            </div>
                        </td>
                        {{-- TIPO MEMBRESIA --}}
                        <td class="min-w-72 px-6 py-4">
                            {{$socio->descripcion}}
                        </td>
                        <td class="max-w-fit px-6 py-4">
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
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="block w-full justify-end">{{$listaSocios->links()}}</div>
    </div>
</div>
