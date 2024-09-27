<div class="p-3">
    {{-- TITULO --}}
    <h4 class="text-2xl font-bold dark:text-white py-2">Cortesias</h4>
    {{-- INPUTS DE BUSQUEDA --}}
    <form class="flex gap-2 items-end" wire:submit='buscar'>
        @csrf
        {{-- SELECT --}}
        <div>
            <label for="tipo_venta" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Buscar
                por:</label>
            <select id="tipo_venta" wire:model.live='tipo_busqueda'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="FOL" selected>FOLIO</option>
                <option value="FEC">FECHA</option>
            </select>
        </div>
        {{-- INPUTS --}}
        <div class="inline-flex grow">
            @if ($tipo_busqueda == 'FOL')
                <input wire:model='folio_busqueda' type="text" id="folio" placeholder="Folio venta"
                    class="h-10 block w-48 p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            @else
                <input wire:model='fecha_busqueda' type="date" id="fecha"
                    class="h-10 block w-48 p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            @endif
        </div>
        {{-- BOTON DE BUSQUEDA --}}
        <div>
            <button type="button" wire:click="buscar"
                class="h-11 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2  dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                <!--Loading indicator-->
                <div wire:loading wire:target='buscar'>
                    @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
                </div>
                <div wire:loading.remove wire:target='buscar'>
                    Buscar
                </div>
            </button>
        </div>

    </form>
    {{-- TABLA DE VENTAS --}}
    <div>
        <table
            class="overflow-y-auto max-h-96  w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="w-32 px-6 py-3">
                        FOLIO
                    </th>
                    <th scope="col" class="w-1/2 px-6 py-3">
                        NOMBRE
                    </th>
                    <th scope="col" class=" w-48 px-6 py-3">
                        FECHA
                    </th>
                    <th scope="col" class="w-16 px-6 py-3">
                        PUNTO
                    </th>
                    <th scope="col" class="w-28 px-6 py-3">
                        TOTAL
                    </th>
                    <th scope="col" class="px-6 py-3">
                        ACCIONES
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->ventas as $index => $venta)
                    <tr wire:key='{{ $index }}' class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <th scope="row"
                            class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            <a href="{{ route('ventas.ticket', ['venta' => $venta->folio]) }}" target="_blank">
                                <div>
                                    {{ $venta->folio }}
                                </div>
                                <div>
                                    {{ $venta->tipo_venta }}
                                </div>
                            </a>
                        </th>
                        <td class="px-6 py-2">
                            {{ $venta->nombre }}
                        </td>
                        <td class="px-6 py-2">
                            {{ $venta->fecha_apertura }}
                        </td>
                        <td class="px-6 py-2">
                            {{ $venta->clave_punto_venta }}
                        </td>
                        <td class="px-6 py-2">
                            ${{ $venta->total }}
                        </td>
                        <td class="px-6 py-2">
                            <button type="button" wire:click='editarVenta({{ $venta->folio }})'
                                class="text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500">
                                Cortesia
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
    {{-- ACTION MESSAGE --}}
    <x-action-message on='open-action-message'>
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
    {{-- MODAL DE OBSERVACIONES --}}
    <x-modal title="Confirmar transformacion" name='modalObservaciones'>
        <x-slot name='body'>
            <p>Desea confirmar transformacion?</p>
            <p>Para la venta con folio: {{ $folio_seleccionado }}</p>
            <input wire:model='observaciones' type="text" id="folio" placeholder="Observaciones"
                class="h-10 block w-48 p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            @error('observaciones')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror

            {{-- BOTON DE CONFIRMACION --}}
            <div>
                <button type="button" wire:click="confirmarCortesia"
                    class="h-11 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2  dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                    <!--Loading indicator-->
                    <div wire:loading wire:target='confirmarCortesia'>
                        @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
                    </div>
                    <div wire:loading.remove wire:target='confirmarCortesia'>
                        Convertir a cortesia
                    </div>
                </button>
            </div>
        </x-slot>
    </x-modal>
</div>
