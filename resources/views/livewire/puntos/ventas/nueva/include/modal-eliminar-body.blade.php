<div>
    <p>{{ !is_null($ventaForm->indexSeleccionado) ? $ventaForm->productosTable[$ventaForm->indexSeleccionado]['nombre'] : '' }}</p>
    <form class="max-w-sm mx-auto">
        {{-- Select --}}
        <div>
            <label for="countries" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Motivo de
                eliminacion</label>
            <select id="countries" wire:model.live='id_eliminacion'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option selected value="{{ null }}">Seleccione motivo</option>
                @foreach ($this->conceptos as $i => $item)
                    <option value="{{ $item->id }}" wire:key='{{ $i }}'>{{ $item->descripcion }}</option>
                @endforeach
            </select>
            @error('id_eliminacion')
                <x-input-error messages="{{ $message }}" />
            @enderror
        </div>
        {{-- Input text --}}
        @if ($id_eliminacion ? $this->conceptos->find($id_eliminacion)->editable : false)
            <input type="text" placeholder="Detalles ..." wire:model='motivo'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            @error('motivo')
                <x-input-error messages="{{ $message }}" />
            @enderror
        @endif
        {{-- Button --}}
        <button type="button" wire:click='confirmarEliminacion()'
            class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">Confirmar</button>
    </form>

</div>
