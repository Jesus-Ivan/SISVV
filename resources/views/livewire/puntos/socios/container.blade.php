<div class="space-y-4 m-2">
    <div class="flex gap-4">
        <!--USER CARD-->
        <a
            class=" flex gap-4 w-full p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
            <!--USER IMAGE-->
            @if ($socio)
                <img class="w-48 h-48 rounded-full" src="{{ asset($socio->img_path) }}" alt="Imagen de socio">
            @else
                <img class="w-48 h-48 rounded-full" src="https://placehold.co/400" alt="Rounded avatar">
            @endif
            <!--USER INFO-->
            <div>
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                    {{ $socio ? $socio->nombre . ' ' . $socio->apellido_p . ' ' . $socio->apellido_m : '' }}
                </h5>
                <p class="font-normal text-gray-700 dark:text-gray-400">
                    Estado membresia: {{ $socio ? $socio->socioMembresia->estado : '' }}
                </p>
                <p class="font-normal text-gray-700 dark:text-gray-400">
                    Tipo membresia: {{ $socio ? $socio->socioMembresia->membresia->descripcion : '' }}
                </p>
                <p class="font-normal text-gray-700 dark:text-gray-400">
                    No.Socio: {{ $socio ? $socio->id : '' }}
                </p>
                @if ($socio)
                    <div>
                        @if ($socio->socioMembresia->estado == 'CAN')
                            <p class="font-bold text-red-600">ACCESO DENEGADO</p>
                        @else
                            <p class="font-bold text-green-500">ACCESO PERMITIDO</p>
                        @endif
                    </div>
                @endif
                @if ($socio)
                    <div>
                        @if ($socio->firma == '0')
                            <p class="font-bold text-red-600">FIRMA NO AUTORIZADA</p>
                        @else
                            <p class="font-bold text-green-500">FIRMA AUTORIZADA</p>
                        @endif
                    </div>
                @endif
            </div>
        </a>
        <!--Search bar-->
        <div class="w-full">
            <livewire:autocomplete :params="[
                'table' => ['name' => 'socios', 'columns' => ['id', 'nombre', 'apellido_p', 'apellido_m']],
            ]" primaryKey="id" event="on-selected-socio" />
        </div>
    </div>
    <!-- Linea -->
    <hr class="h-px my-3 bg-gray-300 border-0 dark:bg-gray-700">
    <h4 class="text-2xl font-bold dark:text-white mb-4">Integrantes</h4>
    <!-- grid miembros del socio -->
    <div class="grid grid-cols-4 gap-4">
        @if ($socio)
            @foreach ($socio->integrantesSocio as $integrante)
                <div class="p-4 rounded-lg shadow-lg" wire:key ="{{ $integrante->id }}">
                    <!--USER IMAGE-->
                    <div class="w-full">
                        <img class="w-48 h-48 rounded-full" src="{{ asset($integrante->img_path_integrante) }}"
                            alt="Imagen de socio">
                    </div>
                    <!--USER INFO-->
                    <div>
                        <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white">
                            {{ $integrante->nombre_integrante . ' ' . $integrante->apellido_p_integrante . ' ' . $integrante->apellido_m_integrante }}
                        </h5>
                        <p class="font-normal text-gray-700 dark:text-gray-400">
                            {{ $integrante->parentesco }}
                        </p>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
