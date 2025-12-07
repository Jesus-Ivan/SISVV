<div>
    <div class="flex gap-8 items-start">
        <!--SELECT -->
        <div>
            <select id="tipo_venta" wire:model.live="ventaForm.tipo_venta" wire:change='resetVentas'
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="socio" selected>Socios</option>
                <option value="invitado">Invitados del socio</option>
                <option value="general">Publico general</option>
                @if ($this->ventaForm->permisospv->clave_rol != 'MES')
                    <option value="empleado">Empleado</option>
                @endif
            </select>
        </div>
        <!--Autocomplete search component-->
        <div
            class="w-2/6 {{ $this->ventaForm->tipo_venta == 'general' || $this->ventaForm->tipo_venta == 'empleado' ? 'pointer-events-none opacity-60' : '' }}">
            {{-- Componente de busqueda de socios --}}
            <livewire:search-bar tittle="Buscar No. Socio o Nombre" table="socios" :columns="['id', 'nombre', 'apellido_p', 'apellido_m']" primary="id"
                event="on-selected-socio" :conditions="[['deleted_at', '=', $var]]" />
            {{-- Error de validacion --}}
            @error('ventaForm.socio')
                <x-input-error messages="{{ $message }}" />
            @enderror
            {{-- Error de membresia, cancelada --}}
            @if (session('fail_socio'))
                <x-input-error messages="{{ session('fail_socio') }}" />
            @endif
        </div>
        <!--Nombre del invitado del socio -->
        @switch($this->ventaForm->tipo_venta)
            @case('invitado')
                <div>
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
                    @error('ventaForm.nombre_invitado')
                        <x-input-error messages="{{ $message }}" />
                    @enderror
                </div>
            @break

            @case('general')
                <div>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd"
                                    d="M12 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4h-4Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" id="email-address-icon" wire:model="ventaForm.nombre_p_general"
                            class=" bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Nombre del cliente">
                    </div>
                    @error('ventaForm.nombre_p_general')
                        <x-input-error messages="{{ $message }}" />
                    @enderror
                </div>
            @break

            @case('empleado')
                <div>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd"
                                    d="M12 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4h-4Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" id="email-address-icon" wire:model="ventaForm.nombre_empleado"
                            class=" bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Nombre del empleado">
                    </div>
                    @error('ventaForm.nombre_empleado')
                        <x-input-error messages="{{ $message }}" />
                    @enderror
                </div>
            @break

            @default
        @endswitch
    </div>


    <div style="font-weight: bold">
        @if ($this->ventaForm->tipo_venta == 'socio')
            <p>No. de socio: {{ $this->ventaForm->socio ? $this->ventaForm->socio->id : '' }}</p>
            <p>Nombre:
                {{ $this->ventaForm->socio ? $this->ventaForm->socio->nombre . ' ' . $this->ventaForm->socio->apellido_p . ' ' . $this->ventaForm->socio->apellido_m : '' }}
            </p>
        @elseif ($this->ventaForm->tipo_venta == 'invitado')
            <p>No. de socio: {{ $this->ventaForm->socio ? $this->ventaForm->socio->id : '' }}</p>
        @endif
    </div>
</div>
