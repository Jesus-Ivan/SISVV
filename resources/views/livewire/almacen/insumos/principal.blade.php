<div class="m-2">
    {{-- Search bar --}}
    <form class="flex gap-4" wire:submit='search' method="GET">
        @csrf
        {{-- Search input --}}
        <div class="relative flex items-end gap-4 w-full">
            <label for="default-search"
                class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                </svg>
            </div>
            <input wire:model="search_input" type="text"
                class="block w-full p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Nombre del insumo o código" />
        </div>
        <!--SELECT -->
        <select id="Grupo" wire:model='grupo'
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <option value="{{ null }}">SELECCIONE GRUPO</option>
            @foreach ($this->grupos as $item)
                <option wire:key='{{ $item->id }}' value="{{ $item->id }}">{{ $item->descripcion }}</option>
            @endforeach
        </select>
        <!--SELECT ELABORADO -->
        <select id="Grupo" wire:model='tipo_insumo'
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <option value="{{ null }}">TIPO INSUMO</option>
            <option value="1">ELABORADO</option>
            <option value="0">NO ELABORADO</option>
        </select>

        <!--Boton de busqueda -->
        <button type="submit"
            class="w-40 justify-center text-center inline-flex items-center text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">
            <div wire:loading wire:target='search'>
                @include('livewire.utils.loading', ['w' => 5, 'h' => 5])
            </div>
            <div wire:loading.remove wire:target='search'>
                Buscar
            </div>
        </button>
    </form>

    {{-- Tabla de resultados --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-3 py-3 w-fit">
                        #
                    </th>
                    <th scope="col" class="px-6 py-3">
                        INSUMO
                    </th>
                    <th scope="col" class="px-6 py-3">
                        UNIDAD
                    </th>
                    <th scope="col" class="px-6 py-3">
                        GRUPO
                    </th>
                    <th scope="col" class="px-6 py-3">
                        ULTIMA COMPRA
                    </th>
                    <th scope="col" class="px-6 py-3">
                        COSTO C.IVA
                    </th>
                    <th scope="col" class="px-3 py-3">
                        ACCIONES
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->insumos as $index => $item)
                    <tr wire:key='{{ $index }}'
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                        <th scope="row"
                            class="w-fit px-3 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $item->clave }}
                        </th>
                        <td class="px-6 py-2">
                            {{ $item->descripcion }}
                        </td>
                        <td class="px-6 py-2 w-fit">
                            {{ $item->unidad->descripcion }}
                        </td>
                        <td class="px-6 py-2">
                            {{ $item->grupo->descripcion }}
                        </td>

                        <td class="px-6 py-2 w-fit">
                            {{ $item->ultima_compra }}
                        </td>
                        <td class="px-6 py-2">
                            ${{ $item->costo_con_impuesto }}
                        </td>
                        <td class="px-3 py-2">
                            <a href="{{ route('almacen.insumos.editar', $item->clave) }}">
                                <button type="button"
                                    class="text-green-700 hover:text-white border border-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-3.5 py-1.5 text-center dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-600 dark:focus:ring-green-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                        class="w-4 h-4">
                                        <path
                                            d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z" />
                                        <path
                                            d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z" />
                                    </svg>
                                </button>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div>
        {{ $this->insumos->links() }}
    </div>
</div>
