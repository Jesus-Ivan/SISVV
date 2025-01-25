<div class="ms-3 mx-3" x-data @keyup.ctrl.window="$dispatch('open-modal', { name: 'añadirMr' })">
    {{-- SEARCH BAR --}}
    <div class="flex gap-4">
        <div>
            <label for="clave_dpto" class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Origen de
                Merma</label>
            <select id="clave_dpto" wire:model=''
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                <option selected value="{{ null }}">SELECCIONAR</option>
            </select>
        </div>
        <div>
            <label for="st_min" class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Mes</label>
            <input type="date" name="st_min" wire:model='cantidad'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                placeholder="Unitario">
        </div>
    </div>
    {{-- TABLA --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        FOLIO
                    </th>
                    <th scope="col" class="px-6 py-3">
                        ARTICULO
                    </th>
                    <th scope="col" class="px-6 py-3">
                        OBSERVACIONES
                    </th>
                    <th scope="col" class="px-6 py-3">
                        CANTIDAD
                    </th>
                    <th scope="col" class="px-6 py-3">
                        UNIDAD
                    </th>
                    <th scope="col" class="px-6 py-3">
                        ACCIONES
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        1
                    </th>
                    <td class="px-6 py-4">
                        Silver
                    </td>
                    <td class="px-6 py-4">
                        se echo a perder
                    </td>
                    <td class="px-6 py-4">
                        10
                    </td>
                    <td class="px-6 py-4">
                        KG
                    </td>
                    <td class="px-6 py-4">
                        <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    @include('livewire.almacen.mermas.modal-mermas')
</div>
