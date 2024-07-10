<form id="modal-pago-body" class="w-96" wire:submit='agregarPago()'>
    <!-- Modal body -->
    <div>
        {{-- Autocomplete --}}
        <div
            class="{{ $this->ventaForm->tipo_venta == 'general' || $this->ventaForm->tipo_venta == 'empleado' || $this->ventaForm->tipo_venta == 'invitado' ? 'opacity-50 pointer-events-none' : 'opacity-100' }}">
            <livewire:autocomplete :params="[
                'table' => ['name' => 'socios', 'columns' => ['id', 'nombre', 'apellido_p', 'apellido_m']],
            ]" primaryKey="id" event="selected-socio-pago" />
            @if (session('socioActivo'))
                <x-input-error messages="{{ session('socioActivo') }}" />
            @endif
        </div>
        {{-- Info de socio --}}
        <div>
            @if (isset($this->ventaForm->socioPago))
                {{ $this->ventaForm->socioPago->id }} -
                {{ $this->ventaForm->socioPago->nombre . ' ' . $this->ventaForm->socioPago->apellido_p . ' ' . $this->ventaForm->socioPago->apellido_m }}
            @endif
            @error('ventaForm.socioPago')
                <x-input-error messages="{{ $message }}" />
            @enderror
        </div>
        {{-- Metodo de pago --}}
        <div>
            <label for="id_pago" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Metodo</label>
            <select id="id_pago" wire:model='ventaForm.id_pago'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 my-2">
                <option value="{{ null }}" selected>Seleccione</option>
                @foreach ($this->metodosPago as $metodo)
                    <option value="{{ $metodo->id }}">{{ $metodo->descripcion }}</option>
                @endforeach
            </select>
            @error('ventaForm.id_pago')
                <x-input-error messages="{{ $message }}" />
            @enderror
            @if (session('firma'))
                <x-input-error messages="{{ session('firma') }}" />
            @endif
        </div>
        {{-- Monto y propina --}}
        <div>
            <div class="grid grid-flow-col">
                <input type="number" id="monto_pago" wire:model='ventaForm.monto_pago'
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Monto" required />
                <input type="number" id="propina" wire:model='ventaForm.propina'
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Propina" />
            </div>
            @error('ventaForm.monto_pago')
                <x-input-error messages="{{ $message }}" />
            @enderror
        </div>
    </div>
    <!-- Modal footer -->
    <div class="flex items-center mt-4 border-t border-gray-200 rounded-b dark:border-gray-600">
        <button type="submit"
            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            <div wire:loading.delay wire:target='agregarPago' class="me-4">
                @include('livewire.utils.loading', ['w' => 4, 'h' => 4])
            </div>
            Aceptar
        </button>
    </div>
</form>
