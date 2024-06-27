<div class="m-2">
    {{-- Search bar --}}
    <div class="flex gap-3">
        <livewire:autocomplete :params="[
            'table' => ['name' => 'socios', 'columns' => ['id', 'nombre', 'apellido_p', 'apellido_m']],
        ]" primaryKey="id" event="on-selected-socio" />
        <div class="w-5/6">
            <p>No.Socio {{ $socio ? $socio['id'] : '' }}</p>
            <p>Nombre: {{ $socio ? $socio['nombre'] . ' ' . $socio['apellido_p'] . ' ' . $socio['apellido_m'] : '' }}
            </p>
            @error('socio')
                <x-input-error messages="{{ $message }}" />
            @enderror
        </div>
    </div>
    {{-- line --}}
    <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">
    {{-- Inputs, fecha inicio, tasa de incremento, descuento --}}
    <div class="flex justify-between gap-3">
        <div>
            <label for="inicio" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                Inicio de la anualidad
            </label>
            <input type="date" id="inicio" wire:model="fInicio"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                required />
            @error('fInicio')
                <x-input-error messages="{{ $message }}" />
            @enderror
        </div>
        <div>
            <label for="incremento" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                Tasa de incremento anual (%)
            </label>
            <input type="number" id="incremento" wire:model="incrementoAnual"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                required placeholder="5" />
            @error('incrementoAnual')
                <x-input-error messages="{{ $message }}" />
            @enderror

        </div>
        <div>
            <label for="descuento" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                Descuento sobre membresia (%)
            </label>
            <input type="number" id="descuento" wire:model="descuento"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                required />
            @error('descuento')
                <x-input-error messages="{{ $message }}" />
            @enderror
        </div>
    </div>
    {{-- line --}}
    <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">
    {{-- Tittle cuotas y button --}}
    <div class="flex justify-between items-center">
        <p class="text-lg font-bold text-gray-900 dark:text-white">Cuotas</p>
        <button type="button" x-data x-on:click="$dispatch('open-modal', {name:'cargosFijosModal'})"
            class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
            Añadir cuota
        </button>
    </div>
    {{-- Tabla cuotas --}}
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
                    <th scope="col" class="px-6 py-3 w-32">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($listaCuotas as $index => $cuota)
                    <tr wire:click='{{ $index }}'
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-2">
                            {{ $cuota['id'] }}
                        </td>
                        <td class="px-6 py-2 ">
                            {{ $cuota['descripcion'] }}
                        </td>
                        <td class="px-6 py-2 h-14">
                            <button type="button" wire:click="removeCuota({{ $index }})"
                                class="text-red-700 border border-red-700 hover:bg-red-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:focus:ring-red-800 dark:hover:bg-red-500">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="w-5 h-5">
                                    <path fill-rule="evenodd"
                                        d="M8.586 2.586A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h3a1 1 0 1 1 0 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8a1 1 0 0 1 0-2h3V4a2 2 0 0 1 .586-1.414ZM10 6h4V4h-4v2Zm1 4a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Zm4 0a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="sr-only">Borrar</span>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{-- line --}}
    <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">
    {{-- Tittle resultados y button calcular --}}
    <div class="flex justify-between items-center">
        <p class="text-lg font-bold text-gray-900 dark:text-white">Resultados</p>
        <button type="button" wire:click='calcular'
            class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
            Calcular
        </button>
    </div>
    {{-- Tabla resultados --}}
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
                    <th scope="col" class="px-6 py-3 w-32">MONTO</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($listaResultados as $item)
                    <tr
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-2">
                            {{ $item['id_cuota'] }}
                        </td>
                        <td class="px-6 py-2 ">
                            {{ $item['descripcion'] }}
                        </td>
                        <td class="px-6 py-2 h-14">
                            ${{ $item['monto'] }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="text-right">
        <p class="text-lg font-semibold text-gray-900 dark:text-white">Total: $
            {{ array_sum(array_column($listaResultados, 'monto')) }}</p>
    </div>
    {{-- Finalizar o cancelar --}}
    <div>
        <button type="button"
            class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">
            Cancelar
        </button>
        <button type="button" wire:click='aplicarAnualidad'
            class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
            <p wire:loading.delay.remove wire:target='aplicarAnualidad'>
                Aplicar anualidad
            </p>
            <!--Loading indicator-->
            <div wire:loading.delay wire:target='aplicarAnualidad'>
                @include('livewire.utils.loading', ['w' => 5, 'h' => 5])
            </div>
        </button>
    </div>

    <!--MODAL DE CUOTAS-->
    <x-modal title="Seleccionar cuota" name="cargosFijosModal">
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
                            @foreach ($this->cuotas as $index => $cuota)
                                <tr wire:key='{{ $index }}'
                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-6 py-4">
                                        {{ $cuota->descripcion }}
                                    </td>
                                    <td class="px-6 py-4">
                                        ${{ $cuota->monto }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <button type="button" wire:click ="addCuota({{ $cuota }})"
                                            class="text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500">
                                            <svg class="w-5 h-5" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                viewBox="0 0 24 24">
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
