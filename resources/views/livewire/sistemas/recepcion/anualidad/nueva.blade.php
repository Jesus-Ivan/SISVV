<div class="m-2">
    {{-- Search bar --}}
    <div>
        <div class="max-w-screen-sm">
            {{-- Componente de busqueda de socios --}}
            <livewire:search-bar tittle="Buscar No. Socio o Nombre" table="socios" :columns="['id', 'nombre', 'apellido_p', 'apellido_m']" primary="id"
                event="on-selected-socio" :conditions="[['deleted_at', '=', $var]]" />
        </div>
        <div class="w-5/6">
            <div class="grid grid-cols-2">
                <p>
                    No.Socio: {{ $socio ? $socio['id'] : '' }}
                </p>
                <p>
                    Nombre:
                    {{ $socio ? $socio['nombre'] . ' ' . $socio['apellido_p'] . ' ' . $socio['apellido_m'] : '' }}
                </p>
            </div>
            <div class="grid grid-cols-2">
                <p>
                    Membresia: {{ $this->socio_membresia ? $this->socio_membresia->membresia->descripcion : '' }}
                </p>
                <p>
                    Estado: {{ $this->socio_membresia ? $this->socio_membresia->estado : '' }}
                </p>
            </div>
            @error('socio')
                <x-input-error messages="{{ $message }}" />
            @enderror
        </div>
    </div>
    {{-- line --}}
    <hr class="h-1 my-4 bg-gray-200 border-0 dark:bg-gray-700">
    {{-- inicio anualidad, membresia al finalizar, estado al finalizar --}}
    <div class="flex justify-between items-center">
        <div class="flex gap-2">
            {{-- inicio --}}
            <div>
                <label for="inicio" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Inicio anualidad
                </label>
                <input type="date" id="inicio" wire:model.live.debounce.550ms="fInicio"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    required />
                @error('fInicio')
                    <x-input-error messages="{{ $message }}" />
                @enderror
            </div>
            {{-- No de meses --}}
            <div>
                <label for="inicio" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    No. de meses
                </label>
                <input type="number" id="inicio" wire:model.live.debounce.250ms="no_corrida"
                    class="w-24 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    required />
                @error('fInicio')
                    <x-input-error messages="{{ $message }}" />
                @enderror
            </div>
            {{-- Fin anualidad --}}
            <div>
                <label for="inicio" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Fin anualidad
                </label>
                <input type="text" id="inicio" wire:model="fFin"
                    class="w-32 cursor-not-allowed bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    disabled aria-disabled="true" />
            </div>
            {{-- membresia al finalizar --}}
            <div>
                <label for="membresias" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Membresia
                    al
                    finalizar</label>
                <select id="membresias" wire:model='membresia_finalizar'
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="{{ null }}" selected>Seleccione</option>
                    @foreach ($this->membresias as $membresia)
                        <option value="{{ $membresia->clave }}">{{ $membresia->descripcion }}</option>
                    @endforeach
                </select>
                @error('membresia_finalizar')
                    <x-input-error messages="{{ $message }}" />
                @enderror
            </div>
            {{-- estado al finalizar --}}
            <div>
                <label for="estado_finalizar"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Estado al
                    finalizar</label>
                <select id="estado_finalizar" wire:model='estado_finalizar'
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option selected value="MEN">Mensual</option>
                    <option value="INA">Inactiva</option>
                    <option value="ANU">Anual</option>
                    <option value="CAN">Cancelada</option>
                </select>
                @error('estado_finalizar')
                    <x-input-error messages="{{ $message }}" />
                @enderror
            </div>
        </div>
    </div>
    {{-- line --}}
    <hr class="h-1 my-4 bg-gray-200 border-0 dark:bg-gray-700">
    {{-- Datos generales de la anualidad --}}
    <div class="grid grid-cols-2 gap-4">
        {{-- Inputs --}}
        <div>
            <div class="flex gap-4 mt-5">
                {{-- Membresia anterior  --}}
                <div class="w-full">
                    <label for="mem_ant" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Membresia
                        anterior</label>
                    <input type="number" id="mem_ant" wire:model='membresia_anterior'
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="$0" />
                </div>
                {{-- incremento  --}}
                <div class="w-full">
                    <label for="incremento"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Incremento
                        anual</label>
                    <input type="number" id="incremento" wire:model='incremento'
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="$0" />
                </div>
                {{-- Membresia nueva  --}}
                <div class="w-full">
                    <label for="mem_nueva"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Membresia nueva</label>
                    <input type="number" id="mem_nueva" wire:model='membresia_nueva'
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="$0" />
                </div>
            </div>
            <div class="flex gap-4">
                {{-- descuento  --}}
                <div class="w-full">
                    <label for="descuento"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Descuento
                        membresia</label>
                    <input type="number" wire:model='descuento'
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="$0" />
                </div>
                {{-- iva  --}}
                <div class="w-full">
                    <label for="iva"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">IVA</label>
                    <input type="number" id="iva" wire:model='iva'
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="$0" />
                </div>
            </div>
            <div class="flex gap-4">
                {{-- descuento extra --}}
                <div class="w-full">
                    <label for="descuento"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Descuento
                        Extra</label>
                    <input type="number" wire:model='descuento_extra'
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="$0" />
                </div>
                {{-- Observaciones  --}}
                <div class="w-full">
                    <label for="descuento"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Observaciones</label>
                    <input type="text" wire:model='observaciones'
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Ingrese motivo ..." />
                </div>
            </div>
        </div>
        {{-- Tabla de cuotas para cargar --}}
        <div>
            {{-- tittle cuotas y Boton de cuotas --}}
            <div class="flex justify-between gap-3">
                <div>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">Cuotas</p>
                </div>
                <button type="button" x-data x-on:click="$dispatch('open-modal', {name:'cargosFijosModal'})"
                    class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
                    Buscar cuotas
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
                            <th scope="col" class="px-6 py-3 w-36">
                                Monto
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
                                <td class="px-6 py-2 ">
                                    <input type="number" id="monto-{{ $index }}"
                                        wire:model.live.debounce.500ms='listaCuotas.{{ $index }}.monto'
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="$0" />
                                </td>
                                <td class="px-6 py-2 h-14">
                                    <button type="button" wire:click="removeCuota({{ $index }})"
                                        class="text-red-700 border border-red-700 hover:bg-red-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:focus:ring-red-800 dark:hover:bg-red-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                            fill="currentColor" class="w-5 h-5">
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
                    <tfoot>
                        <tr>
                            <td></td>
                            <td></td>
                            <td class="text-right">${{ number_format($total, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                @error('listaCuotas')
                    <x-input-error messages="{{ $message }}" />
                @enderror
            </div>
        </div>
    </div>
    {{-- line --}}
    <hr class="h-1 my-4 bg-gray-200 border-0 dark:bg-gray-700">
    {{-- Finalizar o cancelar --}}
    <div class="flex gap-4">
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
        <div class="flex items-center">
            <input id="pagado" type="checkbox" wire:model ='saldo_cero'
                class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
            <label for="pagado" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Saldo cero</label>
        </div>
    </div>
    {{-- ALERT MESSAGE --}}
    <x-action-message on='action-message-venta'>
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
                        <input type="text" wire:model.live.debounce.500ms="search"
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
