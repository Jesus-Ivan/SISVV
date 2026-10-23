<div class="space-y-6">
    <!-- Notificaciones / Alertas -->
    @if ($successMessage)
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 flex justify-between items-center" role="alert">
            <div class="flex items-center">
                <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                </svg>
                <span>{{ $successMessage }}</span>
            </div>
            <button wire:click="$set('successMessage', '')" class="text-green-800 dark:text-green-400 font-bold ml-4">&times;</button>
        </div>
    @endif

    @if ($errorMessage)
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 flex justify-between items-center" role="alert">
            <div class="flex items-center">
                <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z"/>
                </svg>
                <span>{{ $errorMessage }}</span>
            </div>
            <button wire:click="$set('errorMessage', '')" class="text-red-800 dark:text-red-400 font-bold ml-4">&times;</button>
        </div>
    @endif

    <!-- Barra de Herramientas y Filtros -->
    <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            
            <!-- Barra de Búsqueda -->
            <div class="relative flex-1 max-w-md">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
                <input wire:model.live.debounce.400ms="search" type="text"
                    class="block w-full p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Buscar por ID, nombre, apellidos, RFC...">
            </div>

            <!-- Botón Crear Nuevo Empleado -->
            <div>
                <button wire:click="create" type="button"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nuevo Empleado
                </button>
            </div>
        </div>

        <!-- Filtros secundarios -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
            <!-- Tipo Empleado (Por defecto: Planta) -->
            <div>
                <label class="block mb-1 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Tipo de Empleado</label>
                <select wire:model.live="tipo_empleado"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="PLANTA">De Planta (Fijos)</option>
                    <option value="TEMPORAL">Temporales (Eventuales)</option>
                    <option value="TODOS">Todos los tipos</option>
                </select>
            </div>

            <!-- Estado Activo / Inactivo -->
            <div>
                <label class="block mb-1 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Estatus</label>
                <select wire:model.live="filtro_activo"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="1">Solo Activos</option>
                    <option value="0">Inactivos / Bajas</option>
                    <option value="todos">Todos</option>
                </select>
            </div>

            <!-- Filtro Área -->
            <div>
                <label class="block mb-1 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Área / Departamento</label>
                <select wire:model.live="filtro_area"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">Todas las áreas</option>
                    @foreach ($areas as $area)
                        <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Resumen Rápido -->
            <div class="flex items-center justify-end text-sm text-gray-500 dark:text-gray-400">
                <span>Total encontrados: <strong class="text-gray-900 dark:text-white">{{ $empleados->total() }}</strong></span>
            </div>
        </div>
    </div>

    <!-- Tabla de Empleados -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th scope="col" class="px-4 py-3.5 w-16 text-center">ID</th>
                        <th scope="col" class="px-4 py-3.5">Empleado</th>
                        <th scope="col" class="px-4 py-3.5">Puesto / Área</th>
                        <th scope="col" class="px-4 py-3.5">Tipo</th>
                        <th scope="col" class="px-4 py-3.5 text-right">Salario Base</th>
                        <th scope="col" class="px-4 py-3.5 text-right">Saldo Adelantos</th>
                        <th scope="col" class="px-4 py-3.5 text-center">Estado</th>
                        <th scope="col" class="px-4 py-3.5 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($empleados as $empleado)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition {{ !$empleado->activo ? 'opacity-60 bg-gray-50/50' : '' }}">
                            <!-- ID Empleado (clave directa) -->
                            <td class="px-4 py-3 text-center font-bold text-gray-900 dark:text-white">
                                #{{ $empleado->id }}
                            </td>

                            <!-- Foto y Nombre -->
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if ($empleado->foto_url)
                                        <img class="w-10 h-10 rounded-full object-cover border border-gray-200 dark:border-gray-600"
                                            src="{{ $empleado->foto_url }}" alt="{{ $empleado->nombre_completo }}">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200 flex items-center justify-center font-bold text-sm">
                                            {{ strtoupper(substr($empleado->nombre, 0, 1) . substr($empleado->apellido_paterno, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-semibold text-gray-900 dark:text-white">
                                            {{ $empleado->nombre_completo }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                            @if($empleado->telefono)
                                                <span>📞 {{ $empleado->telefono }}</span>
                                            @endif
                                            @if($empleado->rfc)
                                                <span>RFC: {{ $empleado->rfc }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Puesto y Área -->
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900 dark:text-white">
                                    {{ $empleado->puesto ? $empleado->puesto->nombre : 'Sin asignar' }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $empleado->area ? $empleado->area->nombre : 'Sin área' }}
                                </div>
                            </td>

                            <!-- Tipo de Empleado -->
                            <td class="px-4 py-3">
                                @if ($empleado->tipo_empleado === 'PLANTA')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                        Planta
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300">
                                        Temporal
                                    </span>
                                @endif
                            </td>

                            <!-- Salario Base -->
                            <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white">
                                ${{ number_format($empleado->salario_base, 2) }}
                                <div class="text-[10px] text-gray-400 uppercase">
                                    {{ $empleado->tipo_pago_predeterminado }}
                                </div>
                            </td>

                            <!-- Saldo Adelantos -->
                            <td class="px-4 py-3 text-right">
                                @if ($empleado->saldo_adelantos > 0)
                                    <span class="font-bold text-red-600 dark:text-red-400">
                                        ${{ number_format($empleado->saldo_adelantos, 2) }}
                                    </span>
                                @else
                                    <span class="text-gray-400">$0.00</span>
                                @endif
                            </td>

                            <!-- Estado -->
                            <td class="px-4 py-3 text-center">
                                <button wire:click="toggleActivo({{ $empleado->id }})" title="Clic para alternar estado"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium cursor-pointer transition {{ $empleado->activo ? 'bg-green-100 text-green-800 hover:bg-green-200 dark:bg-green-900/40 dark:text-green-300' : 'bg-red-100 text-red-800 hover:bg-red-200 dark:bg-red-900/40 dark:text-red-300' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $empleado->activo ? 'bg-green-600' : 'bg-red-600' }}"></span>
                                    {{ $empleado->activo ? 'Activo' : 'Baja' }}
                                </button>
                            </td>

                            <!-- Acciones -->
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <!-- Editar -->
                                    <button wire:click="edit({{ $empleado->id }})"
                                        class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg dark:text-blue-400 dark:hover:bg-gray-700 transition"
                                        title="Editar empleado">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    <!-- Eliminar / Desactivar -->
                                    <button wire:click="confirmDelete({{ $empleado->id }})"
                                        class="p-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg dark:text-red-400 dark:hover:bg-gray-700 transition"
                                        title="Eliminar o dar de baja">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <p class="text-base font-semibold">No se encontraron empleados</p>
                                    <p class="text-xs text-gray-400 mt-1">Prueba cambiando los filtros o registra uno nuevo.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($empleados->hasPages())
            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                {{ $empleados->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Formulario Empleado (Crear / Editar) -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden my-8">
                
                <!-- Cabecera del Modal -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="p-2 bg-blue-100 text-blue-600 rounded-lg dark:bg-blue-900 dark:text-blue-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </span>
                        {{ $isEditing ? "Editar Empleado #{$form->empleado?->id} - {$form->nombre} {$form->apellido_paterno}" : 'Registrar Nuevo Empleado' }}
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Formulario -->
                <form wire:submit="save" class="p-6 space-y-5">
                    @if ($errorMessage)
                        <div class="p-3 text-sm text-red-800 bg-red-50 rounded-lg dark:bg-gray-700 dark:text-red-400">
                            {{ $errorMessage }}
                        </div>
                    @endif

                    <!-- Tipo y Foto -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center bg-gray-50 dark:bg-gray-750 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
                        <!-- Preview Foto -->
                        <div class="flex flex-col items-center">
                            @if ($form->foto)
                                <img src="{{ $form->foto->temporaryUrl() }}" class="w-20 h-20 rounded-full object-cover border-2 border-blue-500 shadow-sm mb-2">
                            @elseif ($isEditing && $form->empleado?->foto_url)
                                <img src="{{ $form->empleado->foto_url }}" class="w-20 h-20 rounded-full object-cover border-2 border-gray-300 mb-2">
                            @else
                                <div class="w-20 h-20 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-400 flex items-center justify-center font-bold text-2xl mb-2">
                                    👤
                                </div>
                            @endif

                            <label class="cursor-pointer text-xs font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                <span>Subir Foto (Opcional)</span>
                                <input type="file" wire:model="form.foto" accept="image/*" class="hidden">
                            </label>
                            @error('form.foto') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Tipo Empleado y Estatus -->
                        <div class="md:col-span-2 space-y-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                                    Tipo de Empleado <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-2 gap-2">
                                    <label class="flex items-center gap-2 p-2.5 rounded-lg border cursor-pointer {{ $form->tipo_empleado === 'PLANTA' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/30' : 'border-gray-200 dark:border-gray-700' }}">
                                        <input type="radio" wire:model="form.tipo_empleado" value="PLANTA" class="text-blue-600">
                                        <span class="text-sm font-medium text-gray-800 dark:text-gray-200">De Planta</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-2.5 rounded-lg border cursor-pointer {{ $form->tipo_empleado === 'TEMPORAL' ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/30' : 'border-gray-200 dark:border-gray-700' }}">
                                        <input type="radio" wire:model="form.tipo_empleado" value="TEMPORAL" class="text-purple-600">
                                        <span class="text-sm font-medium text-gray-800 dark:text-gray-200">Temporal / Eventual</span>
                                    </label>
                                </div>
                                @error('form.tipo_empleado') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex items-center gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" wire:model="form.activo" class="w-4 h-4 text-blue-600 rounded border-gray-300">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Empleado Activo</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Datos Generales -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                                Nombre(s) <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="form.nombre"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Ej. Juan">
                            @error('form.nombre') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                                Primer Apellido <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="form.apellido_paterno"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Ej. Pérez">
                            @error('form.apellido_paterno') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                                Segundo Apellido
                            </label>
                            <input type="text" wire:model="form.apellido_materno"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Ej. López">
                            @error('form.apellido_materno') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Puesto, Área y Salario -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Área / Depto.</label>
                            <select wire:model.live="form.id_area"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Seleccione área</option>
                                @foreach ($areas as $area)
                                    <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Puesto</label>
                            <select wire:model="form.id_puesto"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Seleccione puesto</option>
                                @foreach ($puestos as $puesto)
                                    <option value="{{ $puesto->id }}">{{ $puesto->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                                Salario Base Período <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 start-0 flex items-center ps-3 text-gray-500">$</span>
                                <input type="number" step="0.01" wire:model="form.salario_base"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-7 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="0.00">
                            </div>
                            @error('form.salario_base') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Método de Pago Predeterminado y Fechas -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Pago Predeterminado</label>
                            <select wire:model="form.tipo_pago_predeterminado"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="EFECTIVO">Efectivo</option>
                                <option value="TRANSFERENCIA">Transferencia</option>
                                <option value="MIXTO">Mixto</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Fecha de Ingreso</label>
                            <input type="date" wire:model="form.fecha_ingreso"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Fecha de Baja (Si aplica)</label>
                            <input type="date" wire:model="form.fecha_baja"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>

                    <!-- Datos Opcionales (RFC, CURP, NSS, Teléfono) -->
                    <div class="p-4 bg-gray-50 dark:bg-gray-750 rounded-xl border border-gray-200 dark:border-gray-700">
                        <span class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-3">Datos Fiscales y Contacto (Opcionales)</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Teléfono</label>
                                <input type="text" wire:model="form.telefono"
                                    class="bg-white border border-gray-300 text-gray-900 text-xs rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white w-full"
                                    placeholder="238 123 4567">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">RFC</label>
                                <input type="text" wire:model="form.rfc"
                                    class="bg-white border border-gray-300 text-gray-900 text-xs rounded-lg p-2 uppercase dark:bg-gray-700 dark:border-gray-600 dark:text-white w-full"
                                    placeholder="ABCD000000XXX">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">CURP</label>
                                <input type="text" wire:model="form.curp"
                                    class="bg-white border border-gray-300 text-gray-900 text-xs rounded-lg p-2 uppercase dark:bg-gray-700 dark:border-gray-600 dark:text-white w-full"
                                    placeholder="ABCD000000HXXXXX00">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">NSS</label>
                                <input type="text" wire:model="form.nss"
                                    class="bg-white border border-gray-300 text-gray-900 text-xs rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white w-full"
                                    placeholder="Número Seguro Social">
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" wire:click="closeModal"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 transition flex items-center gap-2">
                            <span wire:loading wire:target="save, form.foto" class="inline-block animate-spin">&#9696;</span>
                            {{ $isEditing ? 'Guardar Cambios' : 'Registrar Empleado' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Confirmación Eliminar / Desactivar -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 p-6 text-center">
                <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 dark:bg-red-900/40 dark:text-red-400 mx-auto flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">
                    ¿Eliminar empleado?
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                    ¿Estás seguro de eliminar a <strong class="text-gray-800 dark:text-gray-200">{{ $empleadoNameToDelete }}</strong>?
                    <br><span class="text-xs text-amber-600 dark:text-amber-400 mt-2 block">Nota: Si el empleado tiene recibos emitidos o movimientos de adelantos registrados, el sistema lo marcará como Inactivo (Baja) para proteger el historial financiero.</span>
                </p>
                <div class="flex items-center justify-center gap-3">
                    <button wire:click="closeDeleteModal" type="button"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                        Cancelar
                    </button>
                    <button wire:click="delete" type="button"
                        class="px-5 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-300">
                        Sí, Continuar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

