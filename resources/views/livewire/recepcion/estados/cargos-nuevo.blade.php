<div>
    <div class="flex">
        <div class="w-full">
            <p>Nombre: {{ $socio->nombre . ' ' . $socio->apellido_p . ' ' . $socio->apellido_m }}</p>
            <p>No.Socio:{{ $socio->id }} </p>
        </div>
        <div class="w-full">
            <p>Membresia: {{ $socioMembresia->membresia->descripcion }}</p>
            <p>
                @if ($socioMembresia->estado == 'ANU')
                    <span
                        class=" inline-flex items-center bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">
                        <span class="w-2 h-2 me-1 bg-green-500 rounded-full"></span>
                        Anualidad activa
                    </span>
                @endif
            </p>
        </div>
    </div>
    {{-- FECHA Y BOTON DE CARGOS --}}
    <div class="{{ $socioMembresia->estado == 'CAN' ? 'flex opacity-50 pointer-events-none' : 'flex' }}">
        {{-- Fecha --}}
        <div class="w-full">
            <input type="date" id="inicio" wire:model="fechaDestino"
                class="w-64 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            @error('fechaDestino')
                <x-input-error messages="{{ $message }}" />
            @enderror
        </div>
        {{-- boton --}}
        <button type="button" x-data x-on:click="$dispatch('open-modal', {name:'cargosModal'})"
            wire:loading.attr="disabled"
            class="h-10 text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
            <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 12h14m-7 7V5" />
            </svg>
            Cargos
        </button>
    </div>
    <!--TABLA DE CARGOS-->
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3 w-32">
                        APLICADO AL
                    </th>
                    <th scope="col" class="px-6 py-3 ">
                        CONCEPTO
                    </th>
                    <th scope="col" class="px-6 py-3 w-32">
                        CARGOS
                    </th>
                    <th scope="col" class="px-6 py-3 w-32">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($listaCargos as $cargoIndex => $cargo)
                    <tr wire:key="{{ $cargoIndex }}"
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <th scope="row"
                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $cargo['fecha'] }}
                        </th>
                        <td class="px-6 py-4">
                            @if ($cargo['tipo'] == $editable_cargo)
                                <input type="text" wire:model='listaCargos.{{ $cargoIndex }}.descripcion'
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                            @else
                                {{ $cargo['descripcion'] }}
                            @endif
                        </td>
                        <td class="px-6 py-4 ">
                            @if ($cargo['tipo'] == $editable_cargo)
                                <div class="flex items-center">
                                    $ <input type="text" wire:model='listaCargos.{{ $cargoIndex }}.monto'
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                                </div>
                            @else
                                <div class="flex items-center">
                                    $ {{ $cargo['monto'] }}
                                </div>
                            @endif

                        </td>
                        <td class="px-6 py-4">
                            <button type="button" wire:click="removeCuota({{ $cargoIndex }})"
                                wire:loading.attr="disabled" wire:target='removeCuota, guardarCambios'
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
    <!--Linea -->
    <hr class="h-px my-4 bg-gray-300 border-0 dark:bg-gray-700">
    {{-- Seccion de cargos fijos --}}
    <div class="{{ $socioMembresia->estado == 'CAN' ? ' opacity-50 pointer-events-none' : '' }}">
        <div class="grid grid-cols-2 gap-4">
            {{-- La tabla que contiene los cargos mensuales --}}
            <div>
                @include('livewire.recepcion.estados.include.cargos-fijos')
            </div>
            {{-- Tabla de cargos incluidos en la aualidad(opcional) --}}
            <div class="mt-10">
                <p
                    class="p-2 text-lg font-semibold text-left rtl:text-right text-gray-900 dark:text-white dark:bg-gray-800">
                    Cargos incluidos en la anualidad
                </p>
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    Clave
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Concepto
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Cargo
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cargos_anualidad as $index => $item)
                                <tr wire:key='{{ $index }}'
                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td scope="row"
                                        class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $item->id_cuota }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $item->descripcion }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $item->monto }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!--BOTONES DE FINALIZADO-->
    <div>
        <a type="button" href="{{ route('recepcion.estado') }}"
            class="inline-flex items-center focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">
            <svg class="w-6 h-6 dark:text-gray-800 text-white me-2" aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                viewBox="0 0 24 24">
                <path
                    d="M14.502 7.046h-2.5v-.928a2.122 2.122 0 0 0-1.199-1.954 1.827 1.827 0 0 0-1.984.311L3.71 8.965a2.2 2.2 0 0 0 0 3.24L8.82 16.7a1.829 1.829 0 0 0 1.985.31 2.121 2.121 0 0 0 1.199-1.959v-.928h1a2.025 2.025 0 0 1 1.999 2.047V19a1 1 0 0 0 1.275.961 6.59 6.59 0 0 0 4.662-7.22 6.593 6.593 0 0 0-6.437-5.695Z" />
            </svg>
            Regresar
        </a>
        <button type="button" wire:click='guardarCambios' wire:target='removeCuota, guardarCambios, addCuota'
            wire:loading.attr="disabled"
            class="inline-flex items-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
            <svg wire:loading.delay.remove wire:target='guardarCambios'
                class="w-6 h-6 dark:text-gray-800 text-white me-2" aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                viewBox="0 0 24 24">
                <path fill-rule="evenodd"
                    d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z"
                    clip-rule="evenodd" />
            </svg>
            <!--Loading indicator-->
            <div wire:loading.delay wire:target='guardarCambios' class="me-4">
                @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
            </div>
            Guardar cambios
        </button>
    </div>
    <!--MODAL DE CARGOS-->
    <x-modal title="Seleccionar cargo" name="cargosModal">
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
                                <tr wire:key="{{ $index }}"
                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-6 py-4">
                                        {{ $cuota->descripcion }}
                                    </td>
                                    <td class="px-6 py-4">
                                        ${{ $cuota->monto }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <button type="button" wire:click="addCuota({{ $cuota }})"
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
    <!--Alerts-->
    <x-action-message on='action-message-cargos'>
        @if (session('success'))
            <div id="alert-exito"
                class="flex items-center p-4 mb-4 text-green-800 border-t-4 border-green-300 bg-green-50 dark:text-green-400 dark:bg-gray-800 dark:border-green-800"
                role="alert">
                <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                </svg>
                <div class="ms-3 text-sm font-medium">
                    {{ session('success') }}
                </div>
            </div>
        @else
            <div id="alert-error"
                class="flex items-center p-4 mb-4 text-red-800 border-t-4 border-red-300 bg-red-50 dark:text-red-400 dark:bg-gray-800 dark:border-red-800"
                role="alert">
                <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                </svg>
                <div class="ms-3 text-sm font-medium">
                    {{ session('fail') }}
                </div>
            </div>
        @endif
    </x-action-message>
</div>
