<div @keyup.ctrl.window="$dispatch('open-modal', {name:'agregar-productos'})">
    {{-- Contenido --}}
    <form wire:submit="cerrarVenta">
        <!-- Title -->
        <h4 class="text-2xl font-bold dark:text-white mx-2">NUEVA VENTA - RECEPCION</h4>
        <!-- Search Bar -->
        <livewire:recepcion.ventas.nueva.search-bar wire:model='datosSocio' />
        @error('datosSocio')
            <x-input-error messages="{{ $message }}" />
        @enderror
        <!--Boton de articulos -->
        <div class="flex">
            <div class="flex-grow"></div>
            <button type="button" x-data x-on:click="$dispatch('open-modal', {name:'agregar-productos'})"
                class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700 inline-flex items-center">
                <svg class="w-5 h-5 text-white me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd"
                        d="M5 3a1 1 0 0 0 0 2h.687L7.82 15.24A3 3 0 1 0 11.83 17h2.34A3 3 0 1 0 17 15H9.813l-.208-1h8.145a1 1 0 0 0 .979-.796l1.25-6A1 1 0 0 0 19 6h-2.268A2 2 0 0 1 15 9a2 2 0 1 1-4 0 2 2 0 0 1-1.732-3h-1.33L7.48 3.796A1 1 0 0 0 6.5 3H5Z"
                        clip-rule="evenodd" />
                    <path fill-rule="evenodd"
                        d="M14 5a1 1 0 1 0-2 0v1h-1a1 1 0 1 0 0 2h1v1a1 1 0 1 0 2 0V8h1a1 1 0 1 0 0-2h-1V5Z"
                        clip-rule="evenodd" />
                </svg>
                Añadir
            </button>
        </div>
        <!-- Tabla de productos -->
        <livewire:recepcion.ventas.nueva.productos-table wire:model='datosProductos' />
        @error('datosProductos')
            <x-input-error messages="{{ $message }}" />
        @enderror
        <!--Linea -->
        <hr class="h-px my-4 bg-gray-300 border-0 dark:bg-gray-700">
        <!-- Titulo y Boton Metodo de pagos -->
        <div class="flex items-center">
            <!--Titulo de metodo de pago-->
            <div class="flex flex-grow">
                <h5 class="text-xl font-bold dark:text-white">Metodo de pago: </h5>
            </div>
            <!--Boton de metodos de pago -->
            <button type="button" x-data x-on:click="$dispatch('open-modal', {name:'metodos-pago'})"
                class=" inline-flex items-center focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                <svg class="w-6 h-6 text-white dark:text-gray-800 me-2" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                    viewBox="0 0 24 24">
                    <path fill-rule="evenodd"
                        d="M12 14a3 3 0 0 1 3-3h4a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-4a3 3 0 0 1-3-3Zm3-1a1 1 0 1 0 0 2h4v-2h-4Z"
                        clip-rule="evenodd" />
                    <path fill-rule="evenodd"
                        d="M12.293 3.293a1 1 0 0 1 1.414 0L16.414 6h-2.828l-1.293-1.293a1 1 0 0 1 0-1.414ZM12.414 6 9.707 3.293a1 1 0 0 0-1.414 0L5.586 6h6.828ZM4.586 7l-.056.055A2 2 0 0 0 3 9v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2h-4a5 5 0 0 1 0-10h4a2 2 0 0 0-1.53-1.945L17.414 7H4.586Z"
                        clip-rule="evenodd" />
                </svg>
                Añadir pago
            </button>
        </div>
        <!-- Tabla de metodos de pago-->
        <livewire:recepcion.ventas.nueva.pagos-table wire:model='datosPagos' />
        @error('datosPagos')
            <x-input-error messages="{{ $message }}" />
        @enderror
        <!--Botones de navegacion (cancelar y cerrar venta)-->
        <div>
            <a type="button" href="{{ route('recepcion.ventas') }}"
                class="inline-flex items-center focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">
                <svg class="w-6 h-6 dark:text-gray-800 text-white me-2" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                    viewBox="0 0 24 24">
                    <path fill-rule="evenodd"
                        d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm7.707-3.707a1 1 0 0 0-1.414 1.414L10.586 12l-2.293 2.293a1 1 0 1 0 1.414 1.414L12 13.414l2.293 2.293a1 1 0 0 0 1.414-1.414L13.414 12l2.293-2.293a1 1 0 0 0-1.414-1.414L12 10.586 9.707 8.293Z"
                        clip-rule="evenodd" />
                </svg>
                Cancelar
            </a>
            <button type="submit"
                class="inline-flex items-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                <svg wire:loading.delay.remove wire:target='cerrarVenta'
                    class="w-6 h-6 dark:text-gray-800 text-white me-2" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd"
                        d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z"
                        clip-rule="evenodd" />
                </svg>
                <!--Loading indicator-->
                <div wire:loading.delay wire:target='cerrarVenta' class="me-4">
                    @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
                </div>
                Cerrar venta
            </button>
        </div>
        <!--Alerts-->
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
    </form>
    <!--Modal productos -->
    <x-modal name="agregar-productos" title="Agregar productos">
        <x-slot name='body'>
            <livewire:recepcion.ventas.nueva.productos-modal-body />
        </x-slot>
    </x-modal>
    <!--Modal pagos -->
    <x-modal name='metodos-pago' title='Agregar metodo de pago'>
        <x-slot name='body'>
            <livewire:recepcion.ventas.nueva.pagos-modal-body />
        </x-slot>
    </x-modal>
    <!--Script para imprimir el ticket-->
    @include('livewire.puntos.ventas.include.print-script')
</div>
