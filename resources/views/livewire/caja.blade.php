<div class="m-3">
    {{-- Campos de busqueda --}}
    <div class="flex gap-3 ">
        <div>
            <label for="cambio" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cambio
                inicial</label>
            <input type="number" id="cambio" wire:model='cambio'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="1000" required />
            @error('cambio')
                <x-input-error messages="{{ $message }}" />
            @enderror
        </div>
        <div>
            <label for="pv" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Punto de
                venta</label>
            <select id="pv" wire:model='puntoSeleccionado'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option selected value="{{ null }}">Seleccione</option>
                @foreach ($this->puntos as $punto)
                    <option value="{{ $punto->clave }}">{{ $punto->nombre }}</option>
                @endforeach
            </select>
            @error('puntoSeleccionado')
                <x-input-error messages="{{ $message }}" />
            @enderror
        </div>
        <button wire:click='abrirCaja'
            class="mt-7 flex items-center max-h-10 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            {{-- Loading indicator --}}
            <div class="me-2" wire:loading.delay wire:target='abrirCaja'>
                @include('livewire.utils.loading', ['w' => 4, 'h' => 4])
            </div>
            Abrir caja
        </button>
    </div>
    {{-- Tabla de cajas --}}
    <div wire:loading.class='animate-pulse' class="relative shadow-md sm:rounded-lg my-3" wire:target='cierreParcial, cerrarCaja'>
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        CORTE
                    </th>
                    <th scope="col" class="px-6 py-3">
                        FECHA APERTURA
                    </th>
                    <th scope="col" class="px-6 py-3">
                        CIERRE PARCIAL
                    </th>
                    <th scope="col" class="px-6 py-3">
                        FECHA CIERRE
                    </th>
                    <th scope="col" class="px-6 py-3">
                        CAMBIO INICIAL
                    </th>
                    <th scope="col" class="px-6 py-3">
                        PUNTO DE VENTA
                    </th>
                    <th scope="col" class="px-6 py-3">
                        ACCIONES
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->statusCaja as $index => $item)
                    <tr wire:key="{{ $index }}"
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                        <th scope="row"
                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $item->corte }}
                        </th>
                        <td class="px-6 py-4">
                            {{ $item->fecha_apertura }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $item->cierre_parcial }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $item->fecha_cierre }}
                        </td>
                        <td class="px-6 py-4">
                            ${{ $item->cambio_inicial }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $item->puntoVenta->nombre }}
                        </td>
                        <td class="px-6 py-4">
                            <x-dropdown>
                                <x-slot name="trigger">
                                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                        viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-width="4"
                                            d="M12 6h.01M12 12h.01M12 18h.01" />
                                    </svg>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link wire:click="cierreParcial({{ $item->corte }})">Cierre
                                        parcial</x-dropdown-link>
                                    <x-dropdown-link wire:click="cerrarCaja({{ $item->corte }})">Cierre
                                        total</x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <dir>{{ $this->statusCaja->links() }}</dir>
    </div>
    <!--Alerts-->
    <x-action-message on='info-caja'>
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
    {{-- Modal de Advertencia --}}
    <x-modal title="Cuentas abiertas" name="modalAdvertencia">
        <x-slot name='body'>
            <div class="text-center">
                <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <h3 class="mb-5 text-xl font-normal text-gray-500 dark:text-gray-400">¡¡ Precaucion !!
                </h3>
                <p class="text-gray-500 dark:text-gray-400">
                    Actualmente tienes cuentas abiertas, te recomendamos cerrar la ventas primero.
                </p>
            </div>
        </x-slot>
    </x-modal>
</div>
