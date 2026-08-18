<div x-data="{
    visible_ventas: false,
    confirmar() {
        $wire.confirmarTransferencia(this.visible_ventas);
    },
}">
    <!--Info del socio-->
    <div class="m-3">
        <div class="flex justify-between">
            <p>NOMBRE: {{ $venta->id_socio }} - {{ $venta->nombre }}</p>
            <p>TIPO VENTA: {{ $venta->tipo_venta }}</p>
        </div>
        <p>CORTE CAJA: {{ $venta->corte_caja }}</p>
        <p>FECHA APERTURA: {{ $venta->fecha_apertura }}</p>
        <p>PUNTO DE VENTA ORIGEN: {{ $venta->puntoVenta->nombre }}</p>

    </div>
    <!--Linea -->
    <hr class="h-1 my-2 bg-gray-300 border-0 dark:bg-gray-700">
    {{-- TABLA DE PRODUCTOS Y  CAJAS --}}
    <div class="grid grid-cols-2 gap-2">
        {{-- TABLA DE PRODUCTOS --}}
        <div>
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="p-2">
                                Descripcion
                            </th>
                            <th scope="col" class="p-2">
                                Precio
                            </th>
                            <th scope="col" class="p-2">
                                Cantidad
                            </th>
                            <th scope="col" class="p-2">
                                Subtotal
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productos as $i => $prod)
                            <tr
                                class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                <th scope="row"
                                    class="p-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $prod['nombre'] }}
                                    <p class="italic">{{ $prod['observaciones'] }}</p>
                                </th>
                                <td class="p-2">
                                    ${{ $prod['precio'] }}
                                </td>
                                <td class="p-2">
                                    {{ $prod['cantidad'] }}
                                </td>
                                <td class="p-2">
                                    ${{ $prod['subtotal'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        {{-- CAJAS ABIERTAS y checkbox --}}
        <div>
            <label for="cajas" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">CAJAS
                ABIERTAS</label>
            <select id="cajas" wire:model.live='caja_destino'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option selected value="{{ null }}">Seleccione destino</option>
                @foreach ($cajas as $key => $item)
                    <option value="{{ $item['corte'] }}">
                        {{ $item['punto_venta']['nombre'] }} - {{ $item['fecha_apertura'] }}
                    </option>
                @endforeach
            </select>
            <div class="flex mt-4">
                <div class="flex items-center h-5">
                    <input id="helper-checkbox" aria-describedby="helper-checkbox-text" type="checkbox"
                        x-on:click="visible_ventas = !visible_ventas"
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                </div>
                <div class="ms-2 text-sm">
                    <label for="helper-checkbox" class="font-medium text-gray-900 dark:text-gray-300">Unificar con la
                        cuenta destino: </label>
                    <p id="helper-checkbox-text" class="text-xs font-normal text-gray-500 dark:text-gray-300">
                        Requiere una cuenta abierta en el punto de venta de destino para unificar cuentas.
                    </p>
                </div>
            </div>
        </div>
    </div>
    {{-- TABLA DE VENTAS --}}
    <div x-show="visible_ventas" x-transition x-cloak>
        <p class="font-bold text-lg mt-3">
            VENTAS ABIERTAS DE DESTINO
        </p>
        {{-- SEARCH BAR --}}
        <div class="w-96">
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
                <input wire:model.live.debounce.500ms="search" type="text" id="default-search"
                    class="w-full p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Nombre o numero de socio" />
            </div>
        </div>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-2">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="p-2">
                            Folio
                        </th>
                        <th scope="col" class="p-2">
                            Nombre
                        </th>
                        <th scope="col" class="p-2">
                            Fecha
                        </th>
                        <th scope="col" class="p-2">
                            Subtotal
                        </th>
                        <th scope="col" class="p-2">
                            Accion
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($this->ventas as $key => $venta)
                        <tr wire:key='{{ $venta->folio }}' wire:click='marcar({{ $venta->folio }})'
                            class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                            <th scope="row" class="p-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $venta->folio }}
                                <p class="italic"> {{ $venta->tipo_venta }}</p>
                            </th>
                            <td class="p-2">
                                <p class="w-full">{{ $venta->nombre }}</p>
                                @if ($venta->id_socio)
                                    <p class="italic"> {{ $venta->id_socio }}</p>
                                @endif
                            </td>
                            <td class="p-2">
                                {{ $venta->fecha_apertura }}
                            </td>
                            <td class="p-2">
                                ${{ $venta->total }}
                            </td>
                            <td class="p-2">
                                @if ($folio_destino == $venta->folio)
                                    <div class="bg-blue-600 rounded-lg p-2 w-10">
                                        <svg class="w-6 h-6 dark:text-gray-800 text-white me-2" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            fill="currentColor" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd"
                                                d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ACTION MESSAGE --}}
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
    <!--Botones de navegacion (transferir)-->
    <div class="my-3">
        <button type="button" x-on:click="confirmar"
            class="inline-flex items-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
            CONFIRMAR TRANSFERENCIA
        </button>
    </div>

    <!--INDICADOR DE CARGA, DE VENTA-->
    <div wire:loading wire:target='confirmarTransferencia'>
        <x-loading-screen name='loading'>
            <x-slot name='body'>
                <div class="flex">
                    <div class="me-4">
                        @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
                    </div>
                    <p>TRANSFIRIENDO VENTA</p>
                </div>
            </x-slot>
        </x-loading-screen>
    </div>

    <!--Script para cerrar la ventana-->
    @include('livewire.puntos.ventas.include.close-pendiente')
</div>
