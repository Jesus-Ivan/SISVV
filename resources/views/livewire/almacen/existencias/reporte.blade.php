<form action="{{ route('reporte-existencias') }}" method="POST" target="_blank" id="reporte-form"
    @keyup.ctrl.window="$dispatch('open-modal', {name:'modal-productos'})">
    @csrf
    {{-- BARRA DE BUSQUEDA --}}
    <div class="flex m-2 gap-4">
        <div class="flex gap-2 items-center">
            <input type="number" placeholder="Folio de requisicion" wire:model='folio_input'
                wire:keydown.enter='searchRequisicion()'
                class="h-10 max-w-72 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            <!--Loading indicator-->
            <div wire:loading.delay wire:target='searchRequisicion'>
                @include('livewire.utils.loading', ['w' => 6, 'h' => 6])
            </div>
        </div>
        <div class="flex grow">
        </div>
        <div>
            <button type="submit"
                class="h-11 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Generar</button>
        </div>
    </div>

    {{-- Tabla de resultados --}}
    <div class="relative overflow-y-auto shadow-md sm:rounded-lg m-2 max-h-svh">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-2">
                        CODIGO
                    </th>
                    <th scope="col" class="px-6 py-2">
                        NOMBRE
                    </th>
                    <th scope="col" class="px-6 py-2">
                        ACCIONES
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lista_articulos as $index => $articulo)
                    <tr wire:model='articulo.{{ $index }}'
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                        <th scope="row"
                            class="w-16 px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            <input class="border-0 pointer-events-none p-0" type="text"
                                name='articulo.{{ $index }}' value="{{ $articulo['codigo'] }}">
                        </th>
                        <td class="px-6 py-2">
                            {{ $articulo['nombre'] }}
                        </td>
                        <td class="px-6 py-2">
                            <a wire:click='removeItem({{ $index }})'
                                class="font-medium text-red-600 dark:text-red-500 hover:underline">Eliminar</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!--Modal productos -->
    <x-modal name="modal-productos" title="Agregar productos">
        <x-slot name='body'>
            <!-- Modal body -->
            <div class="shadow-md sm:rounded-lg">
                <!-- Search bar-->
                <div class="pb-4 w-full bg-white dark:bg-gray-900">
                    <label for="table-search" class="sr-only">Descripcion</label>
                    <div class="relative mt-1">
                        <div
                            class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg wire:loading.remove.delay wire:target='seachProduct'
                                class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                            <!--Loading indicator-->
                            <div wire:loading.delay wire:target='seachProduct'>
                                @include('livewire.utils.loading', ['w' => 5, 'h' => 5])
                            </div>
                        </div>
                        <input type="text" wire:model.live.debounce.500ms ="seachProduct"
                            class="w-full block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Descripcion">
                    </div>
                </div>
                <!-- Result table-->
                <div class="overflow-y-auto h-96">
                    <table class="max-w-2xl text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="p-4">
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    CODIGO
                                </th>
                                <th scope="col" class="py-3">
                                    DESCRIPCION
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    COSTO
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($this->productosResult as $producto)
                                <tr wire:key="{{ $producto->codigo }}"
                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600">
                                    <td class="w-4 p-4">
                                        <input id="checkbox-{{ $producto->codigo }}"
                                            wire:model="selected.{{ $producto->codigo }}" type="checkbox"
                                            class="w-6 h-6 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    </td>
                                    <th scope="row"
                                        class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $producto->codigo }}
                                    </th>
                                    <td class="w-full font-medium text-gray-900  dark:text-white">
                                        <div class="flex items-center">
                                            <label for="checkbox-{{ $producto->codigo }}"
                                                class="w-full py-4 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                                {{ $producto->nombre }}
                                            </label>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                        ${{ $producto->costo_unitario }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Modal footer -->
            <div class="flex items-center mt-4 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button type="button" wire:click='finishSelect'
                    class="flex items-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    <div wire:loading.delay wire:target='finishSelect' class="me-4">
                        @include('livewire.utils.loading', ['w' => 4, 'h' => 4])
                    </div>
                    Aceptar
                </button>
            </div>
        </x-slot>
    </x-modal>
</form>
@script
    <script>
        const formulario = document.getElementById('reporte-form');
        const inputs = formulario.querySelectorAll('input[type="text"], input[type="number"]');
        inputs.forEach(input => {
            input.addEventListener('keypress', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                }
            });
        });
    </script>
@endscript
