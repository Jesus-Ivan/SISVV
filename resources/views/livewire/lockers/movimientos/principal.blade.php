<div>
    {{-- Barra busqueda --}}
    <form class="flex gap-3" wire:submit='$refresh'>
        {{-- Barra de busqueda --}}
        <div class="w-96 ms-2">
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
                <input wire:model="search" type="text" id="search-mov"
                    class="w-full p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="No.socio, nombre, observaciones" />
            </div>
        </div>
        {{-- fecha --}}
        <div class="flex grow">
            <div class="w-fit">
                <input type="date" wire:model="date_search"
                    class="mx-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            </div>
        </div>
        <!--Boton de busqueda -->
        <button wire:click='$refresh' type="submit"
            class="w-32 mx-3 justify-center text-center inline-flex items-center text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">
            <div wire:loading.delay wire:target='refresh' class="me-4">
                @include('livewire.utils.loading', ['w' => 5, 'h' => 5])
            </div>
            Buscar
        </button>
    </form>
    {{-- Tabla movimientos --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg m-2">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        # Locker
                    </th>
                    <th scope="col" class="px-6 py-3">
                        #Socio
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Nombre/Integrante
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Movimiento
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Observaciones
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Fecha
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->movimientos as $i => $mov)
                    <tr wire:key='{{ $i }}'
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                        <th scope="row"
                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $mov->locker->numero }} - 
                            {{ $mov->locker->seccion }}
                        </th>
                        <td class="px-6 py-4">
                            {{ $mov->id_socio }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $mov->nombre }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $mov->tipo_movimiento }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $mov->observaciones }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $mov->fecha_movimiento }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div>
        {{ $this->movimientos->links() }}
    </div>
</div>
