<div>
    {{--Titulo y boton--}}
    {{--Barra de acciones--}}
    <div class="flex justify-between items-end">
        <div class="flex gap-2 items-end">
            {{-- BODEGA --}}
            <form class="w-fit">
                <label for="bodega" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Bodega</label>
                <select id="bodega" disabled wire:model='clave_bodega'
                    class="cursor-not-allowed bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option selected value="{{ null }}">SELECCIONAR BODEGA</option>
                    @foreach ($this->bodegas as $index => $item)
                        <option wire:key='{{ $index }}' value="{{ $item->clave }}">
                            {{ $item->descripcion }}
                        </option>
                    @endforeach
                </select>
            </form>
            {{-- FECHA --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Fecha Existencias</label>
                <input type="text" id="fecha" aria-label="disabled input" wire:model='fecha_inv'
                    class="w-32 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-1.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    disabled>
            </div>
            {{-- HORA --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Hora Existencias</label>
                <input type="text" id="hora" aria-label="disabled input" wire:model='hora_inv'
                    class="w-32 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block  p-1.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    disabled>
            </div>
            {{-- OBSERVACIONES --}}
            <div>
                <input type="text" wire:model='observaciones'
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block  p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Observaciones" />
            </div>
        </div>

        <div class="flex gap-2 items-end">
            {{-- INVENTARIO TEORICO --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Inventario Teórico</label>
                <input type="text" id="inv-teorico" aria-label="disabled input" wire:model='total_inv_teorico'
                    class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    value="$ 0.00" disabled>
            </div>
            {{-- INVENTARIO FISICO --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Inventario Físico</label>
                <input type="text" id="inv-fisico" aria-label="disabled input" wire:model='total_inv_real'
                    class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    value="$ 0.00" disabled>
            </div>
            {{-- diferencia --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Diferencia</label>
                <input type="text" id="diferencia" aria-label="disabled input" wire:model='total_diferencia'
                    class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    value="$ 0.00" disabled>
            </div>
        </div>
    </div>
</div>
