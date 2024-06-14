<div>
    <div class="flex gap-8 items-end m-2">
        <!--SELECT -->
        <div>
            <label for="tipo-venta" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipo de
                venta</label>
            <select id="tipo-venta" wire:model.live="ventaForm.tipoVenta"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="socio" selected>Socios</option>
                <option value="invitado">Invitados del socio</option>
                <option value="general">Publico general</option>
            </select>
        </div>
        <!--Autocomplete search component-->
        <div class="w-2/6 {{ $this->ventaForm->tipoVenta == 'general' ? 'pointer-events-none opacity-60' : '' }}">
            <livewire:autocomplete :params="[
                'table' => ['name' => 'socios', 'columns' => ['id', 'nombre', 'apellido_p', 'apellido_m']],
            ]" primaryKey="id" event="on-selected-socio" />
        </div>
        <!--Nombre del invitado del socio -->
        @if ($this->ventaForm->tipoVenta == 'invitado')
            <div class="relative">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd"
                            d="M12 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4h-4Z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <input type="text" id="email-address-icon" wire:model="ventaForm.nombre_invitado"
                    class=" bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Nombre del invitado">
            </div>
        @endif
    </div>
    <!--Info del socio-->
    <div class="m-3">
        <p>Nombre: {{ $this->ventaForm->socioSeleccionado ? $this->ventaForm->socioSeleccionado->nombre : '' }}</p>
        <p>No. de socio: {{ $this->ventaForm->socioSeleccionado ? $this->ventaForm->socioSeleccionado->id : '' }}</p>
    </div>
</div>