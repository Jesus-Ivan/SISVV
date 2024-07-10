<div>
    <div class="grid grid-flow-col gap-4 {{ $invitado ? 'pointer-events-none' : '' }}">
        <!--Autocomplete search component-->
        <livewire:autocomplete :params="[
            'table' => ['name' => 'socios', 'columns' => ['id', 'nombre', 'apellido_p', 'apellido_m']],
        ]" primaryKey="id" event="on-selected-socio" />
        <!--Info -->
        <div>
            @if ($socioSeleccionado)
                <p>Nombre:
                    {{ $socioSeleccionado['nombre'] . ' ' . $socioSeleccionado['apellido_p'] . ' ' . $socioSeleccionado['apellido_m'] }}
                </p>
                <p>No. de socio: {{ array_key_exists('id', $socioSeleccionado) ? $socioSeleccionado['id'] : '' }}</p>
            @endif
        </div>
    </div>
    @if (session('fail_socio'))
        <x-input-error messages="{{ session('fail_socio') }}" />
    @endif
    <!--Toggle switch -->
    <label class="inline-flex items-center m-2 cursor-pointer">
        <input type="checkbox" class="sr-only peer" wire:model.live ="invitado">
        <div
            class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
        </div>
        <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Cliente invitado</span>
    </label>
    <!--Linea -->
    <hr class="h-px my-2 bg-gray-300 border-0 dark:bg-gray-700">
</div>
