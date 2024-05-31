<div class="m-2">
    <form>
        <!-- Search bar -->
        <div class="grid grid-flow-col">
            <!--No de socio -->
            <div class="relative max-w-lg">
                <!--Autocomplete search component-->
                <livewire:autocomplete :params="[
                    'table' => ['name' => 'socios', 'columns' => ['id', 'nombre', 'apellido_p', 'apellido_m']],
                ]" primaryKey="id" event="on-selected-socio" />
            </div>
            <!--Info -->
            <div>
                <p>Nombre: {{ $socio->nombre . ' ' . $socio->apellido_p . ' ' . $socio->apellido_m }}</p>
                <p>No. de socio: {{ $socio->id }}</p>
                @error('socio')
                    <x-input-error messages="{{ $message }}" />
                @enderror
            </div>
        </div>
        <!-- Observaciones y metodo pago -->
        <div class="flex gap-2">
            <!-- Observaciones -->
            <div>
                <label for="observaciones"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Observaciones</label>
                <input type="text" id="observaciones" wire:model="observaciones"
                    class="min-w-64 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block  p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            </div>
            <!-- Metodo Pago general -->
            <div>
                <label for="metodos_pago_general"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Aplicar metodo de pago</label>
                <select id="metodos_pago_general" wire:model='metodoPagoGeneral'
                    wire:change="aplicarMetodosPago($event.target.value)"
                    class="min-w-64 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="{{ null }}" selected>Seleccione</option>
                    @foreach ($this->listaPagos as $pago)
                        <option value="{{ $pago->id }}">{{ $pago->descripcion }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <!--Linea -->
        <hr class="h-px my-2 bg-gray-300 border-0 dark:bg-gray-700">
        <!--Boton de cargos -->
        <div class="flex">
            <div class="flex-grow"></div>
            <!--Boton de saldo a favor -->
            <button type="button" x-data x-on:click="$dispatch('open-modal', {name:'saldo-favor'})"
                class="{{ count($this->saldoFavorDisponible) > 0 ? '' : 'opacity-30 pointer-events-none' }} text-green-700 hover:text-white border border-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-600 dark:focus:ring-green-800">Saldo
                a favor
            </button>
            <button type="button" x-data x-on:click="$dispatch('open-modal', {name:'agregar-cargos'})"
                class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700 inline-flex items-center">
                <svg class="w-5 h-5 text-white me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12 7.205c4.418 0 8-1.165 8-2.602C20 3.165 16.418 2 12 2S4 3.165 4 4.603c0 1.437 3.582 2.602 8 2.602ZM12 22c4.963 0 8-1.686 8-2.603v-4.404c-.052.032-.112.06-.165.09a7.75 7.75 0 0 1-.745.387c-.193.088-.394.173-.6.253-.063.024-.124.05-.189.073a18.934 18.934 0 0 1-6.3.998c-2.135.027-4.26-.31-6.3-.998-.065-.024-.126-.05-.189-.073a10.143 10.143 0 0 1-.852-.373 7.75 7.75 0 0 1-.493-.267c-.053-.03-.113-.058-.165-.09v4.404C4 20.315 7.037 22 12 22Zm7.09-13.928a9.91 9.91 0 0 1-.6.253c-.063.025-.124.05-.189.074a18.935 18.935 0 0 1-6.3.998c-2.135.027-4.26-.31-6.3-.998-.065-.024-.126-.05-.189-.074a10.163 10.163 0 0 1-.852-.372 7.816 7.816 0 0 1-.493-.268c-.055-.03-.115-.058-.167-.09V12c0 .917 3.037 2.603 8 2.603s8-1.686 8-2.603V7.596c-.052.031-.112.059-.165.09a7.816 7.816 0 0 1-.745.386Z" />
                </svg>
                Ver cargos
            </button>
        </div>
        <!--Tabla de cargos-->
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            @include('livewire.recepcion.cobros.nuevo.include.cargos-table')
            @error('cargosTabla')
                <x-input-error messages="{{ $message }}" />
            @enderror
        </div>
        <!--Botones de navegacion (cancelar y aplicar cobro)-->
        <div>
            <a type="button" href="{{ route('recepcion.cobros') }}"
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
            <button type="button" wire:click="aplicarCobro" wire:loading.attr="disabled"
                class="inline-flex items-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                <svg wire:loading.delay.remove wire:target='aplicarCobro'
                    class="w-6 h-6 dark:text-gray-800 text-white me-2" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                    viewBox="0 0 24 24">
                    <path fill-rule="evenodd"
                        d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z"
                        clip-rule="evenodd" />
                </svg>
                <!--Loading indicator-->
                <div wire:loading.delay wire:target='aplicarCobro' class="me-4">
                    @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
                </div>
                Aplicar cobro
            </button>
        </div>
    </form>
    <!--Modal large cargos -->
    <x-modal name="agregar-cargos" title="Resumen de estado de cuenta">
        <x-slot name='body'>
            @include('livewire.recepcion.cobros.nuevo.include.modal-body-cargos')
        </x-slot>
    </x-modal>
    <!--Modal saldo a favor -->
    <x-modal name="saldo-favor" title="Saldo a favor">
        <x-slot name='body'>
            @include('livewire.recepcion.cobros.nuevo.include.modal-body-saldo')
        </x-slot>
    </x-modal>
    <!--Alerts-->
    <x-action-message on='action-message-pago'>
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
@script
    <script>
        $wire.on('ver-recibo', (e) => {
            window.open('http://127.0.0.1:8000/recepcion/cobros/recibo/' + e[0].folio, '_blank');
        });
    </script>
@endscript
