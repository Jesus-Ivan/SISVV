<div>
    {{-- Search bar --}}
    <div class="flex gap-4 items-end">
        {{-- Mes busqueda --}}
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Fecha existencias</label>
            <input datepicker type="month" wire:model.live='mes_busqueda'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-36 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        </div>
        {{-- Select de bodega --}}
        <select id="bodega" wire:model.live='clave_bodega'
            class=" bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-fit p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <option selected value="{{ null }}">BODEGA</option>
            @foreach ($this->bodegas as $b)
                <option value="{{ $b->clave }}">{{ $b->descripcion }}</option>
            @endforeach
        </select>
        {{-- Campo de entrada --}}
        <div class="w-64">
            {{-- Componente de busqueda de presentacion o insumos --}}
            @if ($is_presentacion)
                <livewire:search-bar tittle="Buscar presentacion" table="presentaciones" :columns="['clave', 'descripcion']"
                    primary="clave" event="selected-articulo" />
            @else
                <livewire:search-bar tittle="Buscar insumo" table="insumos" :columns="['clave', 'descripcion']" primary="clave"
                    event="selected-articulo" />
            @endif
        </div>
        {{-- BOTON DE BUSQUEDA --}}
        <button type="button" wire:click='buscar'
            class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
            <div wire:loading.remove wire:target='buscar'>
                Todos
            </div>
            <!--Loading indicator-->
            <div wire:loading wire:target='buscar'>
                @include('livewire.utils.loading', ['w' => 5, 'h' => 5])
            </div>
            <span class="sr-only">Buscar</span>
        </button>
    </div>
    {{-- Tabla --}}
    <div wire:loading.class='animate-pulse' class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-2">
                        ENTRADA
                    </th>
                    <th scope="col" class="px-6 py-2">
                        FECHA EXISTENCIAS
                    </th>
                    <th scope="col" class="px-6 py-2">
                        #
                    </th>
                    <th scope="col" class="px-4 py-2">
                        DESCRIPCIÓN
                    </th>
                    <th scope="col" class="px-4 py-2">
                        PROVEEDOR
                    </th>
                    <th scope="col" class="px-4 py-2">
                        CANTIDAD
                    </th>
                    <th scope="col" class="px-4 py-2">
                        COSTO UNITARIO
                    </th>
                    <th scope="col" class="px-6 py-2">
                        IVA
                    </th>
                    <th scope="col" class="px-4 py-2">
                        C.C.IMPUESTO
                    </th>
                    <th scope="col" class="px-4 py-2">
                        TOTAL
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->entradas as $index => $detalle)
                    <tr wire:key='{{ $index }}'
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-2">
                            {{ $detalle->folio_entrada }}
                        </td>
                        <td class="px-6 py-2">
                            {{ $detalle->entrada->fecha_existencias }}
                        </td>
                        <td class="px-6 py-2">
                            {{ $detalle->clave_presentacion ?: $detalle->clave_insumo }}
                        </td>
                        <td class="px-6 py-2">
                            {{ $detalle->descripcion }}
                        </td>
                        <td class="px-6 py-2">
                            {{ $detalle->proveedor->nombre }}
                        </td>
                        <td class="px-6 py-2">
                            {{ $detalle->cantidad }}
                        </td>
                        <td class="px-6 py-2">
                            ${{ $detalle->costo_unitario }}
                        </td>
                        <td class="px-6 py-2">
                            {{ $detalle->iva }}
                        </td>
                        <td class="px-6 py-2">
                            {{ $detalle->costo_con_impuesto }}
                        </td>
                        <td class="px-6 py-2">
                            ${{ $detalle->importe }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{-- PAGINADOR --}}
    <div>
        {{ $this->entradas->links() }}
    </div>
</div>
