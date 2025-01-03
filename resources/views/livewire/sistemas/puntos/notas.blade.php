<div class="p-3">
    {{-- TITULO --}}
    <h4 class="text-2xl font-bold dark:text-white py-2">NOTAS DE VENTA</h4>
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
                <input wire:model='folio_busqueda' type="text" placeholder="Folio venta"
                    class="h-10 block w-48 p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            @else
                <input wire:model='fecha_busqueda' type="date" 
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
                            <a type="button" href="{{ route('sistemas.pv.editar', ['folioventa' => $venta->folio]) }}"
                                class="text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500">
                                Editar
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
