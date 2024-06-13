<div>
    <!--Linea -->
    <hr class="h-px my-4 bg-gray-300 border-0 dark:bg-gray-700">
    <!-- Cargos fijos -->
    <h4 class="text-2xl font-bold dark:text-white ">Cargos fijos</h4>
    {{-- FECHA Y BOTON DE CARGOS --}}
    <div class="flex">
        <div class="w-full">
        </div>
        {{-- boton --}}
        <button type="button" x-data x-on:click="$dispatch('open-modal', {name:'cargosFijosModal'})"
            class=" text-gray-900 hover:text-white border border-gray-800 hover:bg-gray-900 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-gray-600 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-800">
            Agregar
        </button>
    </div>
    <!--TABLA DE CARGOS FIJOS DEL SOCIO-->
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3 w-32">
                        CLAVE
                    </th>
                    <th scope="col" class="px-6 py-3 ">
                        CONCEPTO
                    </th>
                    <th scope="col" class="px-6 py-3 w-64">
                        CARGOS
                    </th>
                    <th scope="col" class="px-6 py-3 w-32">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($listaCargosFijos as $indexFijo => $fijo)
                    <tr wire:key="{{ $indexFijo }}"
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-2">
                            {{ $fijo['cuota']['id'] }}
                        </td>
                        <td class="px-6 py-2 ">
                            {{ $fijo['cuota']['descripcion'] }}
                        </td>
                        <td class="px-6 py-2">
                            ${{ $fijo['cuota']['monto'] }}
                        </td>
                        <td class="px-6 py-2 h-14">
                            @if (!$fijo['cuota']['clave_membresia'])
                                <button type="button" wire:click ="removerCargoFijo({{ $indexFijo }})"
                                    class="text-red-700 border border-red-700 hover:bg-red-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:focus:ring-red-800 dark:hover:bg-red-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                        class="w-5 h-5">
                                        <path fill-rule="evenodd"
                                            d="M8.586 2.586A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h3a1 1 0 1 1 0 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8a1 1 0 0 1 0-2h3V4a2 2 0 0 1 .586-1.414ZM10 6h4V4h-4v2Zm1 4a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Zm4 0a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span class="sr-only">Borrar</span>
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!--MODAL DE CARGOS-->
    <x-modal title="Seleccionar cargo fijo" name="cargosFijosModal">
        <x-slot name="body">
            <!-- Modal body -->
            <div class="max-w-screen-md relative shadow-md sm:rounded-lg">
                <!-- Search bar-->
                <div class="pb-4 w-full bg-white dark:bg-gray-900">
                    <label for="table-search" class="sr-only">Descripcion</label>
                    <div class="relative mt-1">
                        <div
                            class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg wire:loading.delay.remove wire:target='search'
                                class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                            <!--Loading indicator-->
                            <div wire:loading.delay wire:target='search'>
                                @include('livewire.utils.loading', ['w' => 5, 'h' => 5])
                            </div>
                        </div>
                        <input type="text" id="table-search" wire:model.live.debounce.500ms="search"
                            class="w-full block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Descripcion">
                    </div>
                </div>
                <!-- Result table-->
                <div class="overflow-y-auto max-h-full h-96">
                    <table class="text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
                        wire:loading.delay.class="pointer-events-none opacity-50" wire:target='addCuota'>
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3 w-full">
                                    DESCRIPCION
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    COSTO
                                </th>
                                <th scope="col" class="px-6 py-3">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($this->cuotasFijas as $index => $cuota)
                                <tr wire:key="{{ $index }}"
                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-6 py-4">
                                        {{ $cuota->descripcion }}
                                    </td>
                                    <td class="px-6 py-4">
                                        ${{ $cuota->monto }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <button type="button" wire:click="addCuotaFija({{ $cuota }})"
                                            class="text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500">
                                            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                fill="currentColor" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </x-slot>
        <x-slot name='footer'>
            <!--Loading indicator-->
            <div wire:loading.delay wire:target='addCuota'
                class="px-3 py-1 text-xs font-medium leading-none text-center text-blue-800 bg-blue-200 rounded-full animate-pulse dark:bg-blue-900 dark:text-blue-200">
                Verificando cargos registrados ...
            </div>
            @if (session('fail'))
                <x-input-error messages="{{ session('fail') }}" wire:loading.remove wire:target='addCuota' />
            @endif
        </x-slot>
    </x-modal>
</div>
