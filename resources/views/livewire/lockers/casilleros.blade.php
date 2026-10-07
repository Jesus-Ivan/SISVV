<div class="p-2">
    {{-- TITULO --}}
    <h4 class="text-2xl font-bold dark:text-white mx-2">Registro Casilleros</h4>
    {{-- Search bar --}}
    <div class="flex gap-3 my-2" x-data="{
        selected: $wire.status,
        placheholder_text: 'No. Locker o Nombre',
        setStatus(val) {
            this.selected = val;
            switch (val) {
                case $wire.locker_mantenimiento_key:
                    this.placheholder_text = 'No. Locker';
                    break;
                case $wire.locker_disponible_key:
                    this.placheholder_text = 'No. Locker';
                    break;
                case $wire.locker_ocupado_key:
                    this.placheholder_text = 'No. Locker o Nombre';
                    break;
            }
        },
        updateColor(selected_val) {
            const buttonClass = 'px-4 py-2 text-sm font-medium text-gray-900 bg-transparent border-gray-900 hover:bg-gray-900 hover:text-white focus:z-10 focus:ring-2 dark:border-white dark:text-white dark:hover:text-white dark:hover:bg-gray-700';
            const butttonActiveClass = 'px-4 py-2 text-sm font-medium text-white bg-gray-900  border-gray-900 focus:z-10 focus:ring-2 dark:border-white dark:text-white dark:bg-gray-700';
            if (selected_val == this.selected) {
                return butttonActiveClass;
            } else {
                return buttonClass;
            }
        },
        buscar() {
            $wire.buscarCasilleros(this.selected);
        }
    }">
        {{-- AREA --}}
        <select id="countries" wire:model='selected_seccion'
            class="w-fit bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block  p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <option selected value="{{ null }}">Seleccione Area</option>
            @foreach ($secciones as $k => $seccion)
                <option value="{{ $k }}">{{ $seccion }}</option>
            @endforeach
        </select>
        {{-- iNPUT BAR --}}
        <div class="w-full">
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
                <input type="search" wire:model='input_search'
                    class="block w-full p-3 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    x-bind:placeholder="placheholder_text" @keyup.enter="buscar" />
                <button x-on:click='buscar'
                    class="text-white absolute end-2 bottom-2 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-1.5 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Buscar</button>
            </div>
        </div>
        {{-- Button group --}}
        <div class="inline-flex rounded-md shadow-xs" role="group">
            <button type="button" x-on:click="setStatus($wire.locker_mantenimiento_key )"
                x-bind:class="updateColor($wire.locker_mantenimiento_key)" class="border rounded-s-lg ">
                Mantenimiento
            </button>
            <button type="button" x-on:click="setStatus($wire.locker_disponible_key)"
                x-bind:class="updateColor($wire.locker_disponible_key)" class=" border-t border-b">
                Libres
            </button>
            <button type="button" x-on:click="setStatus($wire.locker_ocupado_key)"
                x-bind:class="updateColor($wire.locker_ocupado_key)" class="border rounded-e-lg">
                Ocupados
            </button>
        </div>
    </div>
    {{-- tabla --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        #Locker
                    </th>
                    <th scope="col" class="px-6 py-3">
                        #Socio
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Socio/Integrante
                    </th>
                    <th scope="col" class="px-6 py-3">
                        status
                    </th>
                    <th scope="col" class="px-6 py-3 text-center">
                        Observaciones
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->data_lockers as $key => $item)
                    <tr wire:key='{{ $key }}'
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <th scope="row"
                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $item->numero }} -
                            {{ $item->seccion }}
                        </th>
                        <td class="px-6 py-4">
                            {{ $item->id_socio_actual }}
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-semibold">{{ $item->integrante_nombre ?: $item->socio_nombre }}</p>
                        </td>
                        <td class="px-6 py-4">
                            {{ $item->estado_actual }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <p class="italic">{{ $item->observaciones }}</p>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{-- PAGINADOR --}}
    <div>
        {{ $this->data_lockers->links() }}
    </div>
</div>
