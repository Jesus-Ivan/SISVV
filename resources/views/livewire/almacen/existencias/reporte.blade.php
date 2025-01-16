<div>
    {{-- BARRA DE BUSQUEDA --}}
    <div class="flex m-2 gap-4">
        <div>
            <div>
                <input type="number" placeholder="Folio de requisicion" wire:model='folio_input'
                    wire:keydown.enter='searchRequisicion()'
                    class="max-w-72 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                <div class="flex items-start my-2">
                    <div class="flex items-center h-5">
                        <input id="seleccion" type="checkbox" wire:model='autosuma'
                            class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800" />
                    </div>
                    <label for="seleccion" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Sumar
                        seleccion</label>
                </div>
            </div>
        </div>
        <div class="flex grow">
            <input type="text" placeholder="Codigo o nombre del articulo" wire:model='articulo_input'
                class="min-w-64 max-w-96 w-full h-11 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
        </div>
        <div>
            <button type="button" wire:click='generarReporte'
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
                    <tr wire:model='{{ $index }}'
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                        <th scope="row"
                            class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $articulo['codigo'] }}
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
</div>
