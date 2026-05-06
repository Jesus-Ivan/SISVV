<div>
    {{-- SEARCH BAR --}}
    <form class="flex" method="GET" wire:submit='$refresh'>
        @csrf
        <div class="flex gap-4 w-full">
            {{-- Select Zona de impresion --}}
            <select id="zona" wire:model='id_zona'
                class="block w-fit p-2 text-sm h-10 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option selected value="{{ null }}">Zona impresion</option>
                @foreach ($this->zonas as $item)
                    <option value="{{ $item->id }}">{{ $item->descripcion }}</option>
                @endforeach
            </select>
            <input type="date" wire:model='fecha_busqueda'
                class="w-fit bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />

        </div>
        <span>
            <button type="submit" wire:click='$refresh'
                class="text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center  dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                <svg wire:loading.remove class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                        d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                </svg>
                <!--Loading indicator-->
                <div wire:loading>
                    @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
                </div>
                <span class="sr-only">Buscar</span>
            </button>
        </span>
    </form>
    {{-- CUERPO DEL COMPONENTE --}}
    <livewire:cocina.ordenes.lista-comandas :key="$id_zona" :zona="$id_zona" wire:model="fecha_busqueda" />
</div>
