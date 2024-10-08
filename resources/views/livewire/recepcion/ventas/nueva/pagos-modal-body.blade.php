<form class="w-96" wire:submit='finishPago()'>
    @csrf
    <!-- Modal body -->
    <div class="pb-4 space-y-4">
        {{-- Barra de busqueda de socios --}}
        <div class="{{ $invitado ? 'pointer-events-none' : '' }} ">
            <livewire:autocomplete :params="[
                'table' => ['name' => 'socios', 'columns' => ['id', 'nombre', 'apellido_p', 'apellido_m']],
            ]" primaryKey="id" event='on-selected-socio-pago' />
            {{-- Error de socio cancelado --}}
            @if (session('fail_socio'))
                <x-input-error messages="{{ session('fail_socio') }}" />
            @endif
            {{-- Error de validacion de datos --}}
            @error('socio.nombre')
                <x-input-error messages="{{ $message }}" />
            @enderror
            <div class="flex">
                <p>{{ array_key_exists('id', $socio) ? $socio['id'] : '' }}</p>
                -
                <p>{{ array_key_exists('nombre', $socio) ? $socio['nombre'] . ' ' . $socio['apellido_p'] . ' ' . $socio['apellido_m'] : '' }}
                </p>
            </div>
        </div>
        {{-- Metodo de pago --}}
        <div>
            <label for="countries" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Metodo</label>
            <select id="countries" wire:model='pago' wire:change="selectMetodo($event.target.value)"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="{{ null }}">Seleccione</option>
                @foreach ($this->tiposPago as $tipo)
                    <option value="{{ $tipo->id }}">{{ $tipo->descripcion }}</option>
                @endforeach
            </select>
            {{-- Error de validacion de datos --}}
            @error('pago')
                <x-input-error messages="{{ $message }}" />
            @enderror
            {{-- Error de firma --}}
            @if (session('fail_firma'))
                <x-input-error messages="{{ session('fail_firma') }}" />
            @endif
        </div>
        {{-- Monto y propina --}}
        <div class="flex align-top">
            <div class="w-full">
                <input type="number" id="monto" wire:model='monto'
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Monto" required />
                @error('monto')
                    <x-input-error messages="{{ $message }}" />
                @enderror
            </div>
            <input type="number" id="propina" wire:model='propina'
                class="w-auto max-h-11 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block  p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Propina" />
        </div>
    </div>
    <!-- Boton de confirmar -->
    <button type="submit"
        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
        Aceptar
    </button>
</form>
