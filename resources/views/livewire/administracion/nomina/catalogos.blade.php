<div>
    <div class="space-y-6">
    <!-- Selector de Sub-Pestaña de Catálogo -->
    <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
        <div class="flex gap-2">
            <button wire:click="setTab('areas')" type="button"
                class="px-4 py-2 text-sm font-bold rounded-lg transition {{ $activeTab === 'areas' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                Áreas / Departamentos ({{ $areas->count() }})
            </button>
            <button wire:click="setTab('puestos')" type="button"
                class="px-4 py-2 text-sm font-bold rounded-lg transition {{ $activeTab === 'puestos' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                Puestos de Trabajo ({{ $puestos->count() }})
            </button>
        </div>

        <div>
            @if ($activeTab === 'areas')
                <button wire:click="createArea" type="button"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nueva Área
                </button>
            @else
                <button wire:click="createPuesto" type="button"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nuevo Puesto
                </button>
            @endif
        </div>
    </div>

    <!-- SECCIÓN ÁREAS -->
    @if ($activeTab === 'areas')
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3">Nombre del Área</th>
                        <th class="px-4 py-3">Descripción</th>
                        <th class="px-4 py-3 text-center">Puestos</th>
                        <th class="px-4 py-3 text-center">Empleados</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($areas as $area)
                        <tr wire:key="area-{{ $area->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">
                                {{ $area->nombre }}
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                {{ $area->descripcion ?: 'Sin descripción' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                    {{ $area->puestos_count }} puestos
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300">
                                    {{ $area->empleados_count }} emp.
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button wire:click="toggleAreaActivo({{ $area->id }})"
                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $area->activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $area->activo ? 'Activo' : 'Inactivo' }}
                                </button>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <button wire:click="editArea({{ $area->id }})" class="p-1 text-blue-600 hover:text-blue-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button wire:click="deleteArea({{ $area->id }})" class="p-1 text-red-600 hover:text-red-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-gray-400">No hay áreas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    <!-- SECCIÓN PUESTOS -->
    @if ($activeTab === 'puestos')
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3">Puesto</th>
                        <th class="px-4 py-3">Área Asociada</th>
                        <th class="px-4 py-3 text-right">Salario Sugerido</th>
                        <th class="px-4 py-3 text-center">Empleados</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($puestos as $puesto)
                        <tr wire:key="puesto-{{ $puesto->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">
                                {{ $puesto->nombre }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $puesto->area?->nombre ?: 'Sin área asignada' }}
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-gray-800 dark:text-gray-200">
                                ${{ number_format($puesto->salario_sugerido, 2) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300">
                                    {{ $puesto->empleados_count }} emp.
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button wire:click="togglePuestoActivo({{ $puesto->id }})"
                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $puesto->activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $puesto->activo ? 'Activo' : 'Inactivo' }}
                                </button>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <button wire:click="editPuesto({{ $puesto->id }})" class="p-1 text-blue-600 hover:text-blue-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button wire:click="deletePuesto({{ $puesto->id }})" class="p-1 text-red-600 hover:text-red-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-gray-400">No hay puestos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
    </div>

    <!-- MODAL ÁREA -->
    @if ($showAreaModal)
        <div class="fixed inset-0 z-50 overflow-y-auto m-0" role="dialog" aria-modal="true">
            <!-- Backdrop fijo (cubre el 100% de la pantalla) -->
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity m-0"
                 wire:click="$set('showAreaModal', false)"
                 aria-hidden="true"></div>

            <!-- Contenedor centrado para scroll -->
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 m-0"
                 wire:click.self="$set('showAreaModal', false)">
                <div class="relative w-full max-w-md my-6 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 p-6 text-left overflow-hidden transform transition-all"
                     @click.stop>
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $isEditingArea ? 'Editar Área' : 'Nueva Área' }}
                        </h3>
                        <button wire:click="$set('showAreaModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xl font-bold">&times;</button>
                    </div>

                    <form wire:submit="saveArea" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Nombre del Área *</label>
                            <input type="text" wire:model="areaNombre"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Ej. Restaurante & Bar">
                            @error('areaNombre') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Descripción</label>
                            <textarea wire:model="areaDescripcion" rows="2"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                        </div>

                        <div class="flex items-center gap-2">
                            <input type="checkbox" wire:model="areaActivo" class="w-4 h-4 text-blue-600 rounded">
                            <label class="text-sm text-gray-700 dark:text-gray-300">Área Activa</label>
                        </div>

                        <div class="flex justify-end gap-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button type="button" wire:click="$set('showAreaModal', false)" class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">Cancelar</button>
                            <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL PUESTO -->
    @if ($showPuestoModal)
        <div class="fixed inset-0 z-50 overflow-y-auto m-0" role="dialog" aria-modal="true">
            <!-- Backdrop fijo (cubre el 100% de la pantalla) -->
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity m-0"
                 wire:click="$set('showPuestoModal', false)"
                 aria-hidden="true"></div>

            <!-- Contenedor centrado para scroll -->
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 m-0"
                 wire:click.self="$set('showPuestoModal', false)">
                <div class="relative w-full max-w-md my-6 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 p-6 text-left overflow-hidden transform transition-all"
                     @click.stop>
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $isEditingPuesto ? 'Editar Puesto' : 'Nuevo Puesto' }}
                        </h3>
                        <button wire:click="$set('showPuestoModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xl font-bold">&times;</button>
                    </div>

                    <form wire:submit="savePuesto" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Nombre del Puesto *</label>
                            <input type="text" wire:model="puestoNombre"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Ej. Mesero, Cocinero, Caddie">
                            @error('puestoNombre') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Área Asociada</label>
                            <select wire:model="puestoIdArea"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Selecciona área...</option>
                                @foreach ($areas as $a)
                                    <option value="{{ $a->id }}">{{ $a->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Salario Sugerido ($)</label>
                            <input type="number" step="0.01" wire:model="puestoSalarioSugerido"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Descripción</label>
                            <textarea wire:model="puestoDescripcion" rows="2"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                        </div>

                        <div class="flex items-center gap-2">
                            <input type="checkbox" wire:model="puestoActivo" class="w-4 h-4 text-blue-600 rounded">
                            <label class="text-sm text-gray-700 dark:text-gray-300">Puesto Activo</label>
                        </div>

                        <div class="flex justify-end gap-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button type="button" wire:click="$set('showPuestoModal', false)" class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">Cancelar</button>
                            <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

