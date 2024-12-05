<div>
    <x-modal name="añadirPr" title="AÑADIR NUEVO PROVEEDOR">
        <x-slot:body>
            <form class="md:p-1">
                <div class="grid gap-1 mb-1 grid-cols-2">
                    <div class="col-span-2">
                        <label for="nombre_pr" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre
                            del
                            proveedor</label>
                        <input type="text" name="nombre_pr" id="nombre_pr" wire:model="nombre"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Nombre" required>
                        @error('nombre')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="rfc"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">RFC</label>
                        <input type="text" name="rfc" id="rfc" wire:model="rfc"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="RFC" required>
                        @error('rfc')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="consumo"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Consumo Maximo</label>
                        <input type="number" name="consumo" id="consumo" wire:model="consumo"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Consumo" required>
                        @error('consumo')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="credito"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Crédito</label>
                        <input type="number" name="credito" id="credito" wire:model="credito_compra"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Credito" required>
                        @error('credito_compra')
                            <x-input-error messages="{{ $message }}" />
                        @enderror
                    </div>
                </div>
            </form>
        </x-slot>
        <x-slot:footer>
            <button type="button" wire:click="register"
                class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Aceptar
            </button>
            <button x-on:click="show = false" type="button"
                class="py-2.5 px-5 ms-3 text-sm font-medium text-red-900 focus:outline-none bg-red rounded-lg border border-red-200 hover:bg-red-100 hover:text-red-700 focus:z-10 focus:ring-4 focus:ring-red-100 dark:focus:ring-red-700 dark:bg-red-800 dark:text-red-400 dark:border-red-600 dark:hover:text-white dark:hover:bg-red-700">Cancelar
            </button>
        </x-slot>
    </x-modal>
</div>
