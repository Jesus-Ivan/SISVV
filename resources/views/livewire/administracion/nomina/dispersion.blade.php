<div class="space-y-6">
    <!-- Mensajes de Notificación -->
    @if ($successMessage)
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 flex justify-between items-center shadow-sm">
            <div class="flex items-center">
                <svg class="w-4 h-4 me-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                </svg>
                <span>{{ $successMessage }}</span>
            </div>
            <button wire:click="$set('successMessage', '')" class="text-green-800 dark:text-green-400 font-bold">&times;</button>
        </div>
    @endif

    @if ($errorMessage)
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 flex justify-between items-center shadow-sm">
            <div class="flex items-center">
                <svg class="w-4 h-4 me-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z"/>
                </svg>
                <span>{{ $errorMessage }}</span>
            </div>
            <button wire:click="$set('errorMessage', '')" class="text-red-800 dark:text-red-400 font-bold">&times;</button>
        </div>
    @endif

    <!-- Selector y Tarjetas de Período Activo -->
    <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            
            <!-- Selector de Período -->
            <div class="flex items-center gap-3 flex-1">
                <span class="p-2.5 bg-blue-50 text-blue-600 rounded-lg dark:bg-blue-900/30 dark:text-blue-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </span>
                <div class="flex-1 max-w-md">
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Período de Nómina Seleccionado</label>
                    <select wire:change="selectPeriodo($event.target.value)"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm font-semibold rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @forelse ($periodos as $p)
                            <option value="{{ $p->id }}" {{ $p->id == $selectedPeriodoId ? 'selected' : '' }}>
                                {{ $p->nombre }} ({{ $p->fecha_inicio ? $p->fecha_inicio->format('d/m/Y') : '' }} - {{ $p->fecha_fin ? $p->fecha_fin->format('d/m/Y') : '' }}) [{{ $p->tipo_periodo }}]
                            </option>
                        @empty
                            <option value="">No hay períodos registrados</option>
                        @endforelse
                    </select>
                </div>
            </div>

            <!-- Botones de Gestión de Período -->
            <div class="flex flex-wrap items-center gap-2">
                <button wire:click="createPeriodo" type="button"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nuevo Período
                </button>

                @if ($selectedPeriodo)
                    <button wire:click="editPeriodo({{ $selectedPeriodo->id }})" type="button"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition"
                        title="Modificar fechas o nombre de este período">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        Editar
                    </button>

                    <button wire:click="recalcularTotalesPeriodo" type="button"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition"
                        title="Actualizar sumatorias del período">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Recalcular
                    </button>

                    <button wire:click="confirmDeletePeriodo" type="button"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-red-600 bg-red-50 rounded-lg hover:bg-red-100 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Eliminar
                    </button>
                @endif
            </div>
        </div>

        <!-- Indicadores / Métricas del Período -->
        @if ($selectedPeriodo)
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mt-5 pt-4 border-t border-gray-100 dark:border-gray-700">
                <div class="bg-gray-50 dark:bg-gray-750 p-3 rounded-lg border border-gray-100 dark:border-gray-700">
                    <span class="text-[11px] font-semibold text-gray-500 uppercase">Tipo & Estado</span>
                    <div class="flex items-center gap-1.5 mt-1">
                        <span class="px-2 py-0.5 text-xs font-bold rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            {{ $selectedPeriodo->tipo_periodo }}
                        </span>
                        <span class="text-xs text-gray-600 dark:text-gray-300">
                            {{ $selectedPeriodo->estado }}
                        </span>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-750 p-3 rounded-lg border border-gray-100 dark:border-gray-700">
                    <span class="text-[11px] font-semibold text-gray-500 uppercase">Nómina Fiscal</span>
                    <div class="text-base font-bold text-gray-900 dark:text-white mt-1">
                        ${{ number_format($selectedPeriodo->total_fiscal, 2) }}
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-750 p-3 rounded-lg border border-gray-100 dark:border-gray-700">
                    <span class="text-[11px] font-semibold text-gray-500 uppercase">Dif. Efectivo</span>
                    <div class="text-base font-bold text-emerald-600 dark:text-emerald-400 mt-1">
                        ${{ number_format($selectedPeriodo->total_efectivo, 2) }}
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-750 p-3 rounded-lg border border-gray-100 dark:border-gray-700">
                    <span class="text-[11px] font-semibold text-gray-500 uppercase">Deducciones</span>
                    <div class="text-base font-bold text-red-600 dark:text-red-400 mt-1">
                        ${{ number_format($selectedPeriodo->total_deducciones, 2) }}
                    </div>
                </div>

                <div class="bg-blue-50 dark:bg-blue-950/40 p-3 rounded-lg border border-blue-200 dark:border-blue-800 col-span-2 sm:col-span-1">
                    <span class="text-[11px] font-semibold text-blue-700 dark:text-blue-300 uppercase">Gran Total Neto</span>
                    <div class="text-lg font-black text-blue-900 dark:text-blue-100 mt-0.5">
                        ${{ number_format($selectedPeriodo->total_neto, 2) }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Barra de Herramientas de Recibos (Importar, Exportar, Multi-Impresión) -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            
            <!-- Acciones Rápidas -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- Botón Nuevo Recibo Individual -->
                <button wire:click="createRecibo" type="button"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nuevo Recibo
                </button>

                <!-- Descargar Plantilla Excel -->
                <a href="{{ route('nomina.excel.plantilla', ['id_periodo' => $selectedPeriodoId]) }}" target="_blank"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-emerald-800 bg-emerald-50 border border-emerald-300 rounded-lg hover:bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Plantilla Excel
                </a>

                <!-- Importar Plantilla Excel -->
                <button wire:click="openImportModal" type="button"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12"/>
                    </svg>
                    Importar Excel
                </button>

                <!-- Imprimir Lote Seleccionado (2 por página carta) -->
                @if (count($selectedRecibos) > 0)
                    <a href="{{ route('nomina.pdf.multiple') }}?ids={{ implode(',', $selectedRecibos) }}" target="_blank"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-white bg-purple-600 rounded-lg hover:bg-purple-700 shadow-sm transition animate-pulse">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Imprimir {{ count($selectedRecibos) }} Recibos en PDF
                    </a>
                @endif
            </div>

            <!-- Búsqueda rápida de recibo -->
            <div class="relative w-full md:w-72">
                <input wire:model.live.debounce.300ms="reciboSearch" type="text"
                    class="block w-full p-2 ps-8 text-xs text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                    placeholder="Filtrar por empleado, ID o folio...">
                <div class="absolute inset-y-0 start-0 flex items-center ps-2.5 pointer-events-none">
                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Filtros secundarios -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-3 pt-3 border-t border-gray-100 dark:border-gray-700 text-xs">
            <div>
                <label class="block font-semibold text-gray-500 dark:text-gray-400 mb-1">Tipo de Recibo</label>
                <select wire:model.live="filtroTipoRecibo" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg p-1.5 w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="TODOS">Todos</option>
                    <option value="NOMINA">Nómina</option>
                    <option value="AGUINALDO">Aguinaldo</option>
                    <option value="FINIQUITO">Finiquito</option>
                    <option value="EXTRAORDINARIO">Extraordinario</option>
                </select>
            </div>
            <div>
                <label class="block font-semibold text-gray-500 dark:text-gray-400 mb-1">Estado de Pago</label>
                <select wire:model.live="filtroEstadoPago" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg p-1.5 w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="TODOS">Todos</option>
                    <option value="PENDIENTE">Pendientes</option>
                    <option value="PAGADO">Pagados</option>
                    <option value="PARCIAL">Parciales</option>
                </select>
            </div>
            <div>
                <label class="block font-semibold text-gray-500 dark:text-gray-400 mb-1">Método de Pago</label>
                <select wire:model.live="filtroMetodoPago" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg p-1.5 w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="TODOS">Todos</option>
                    <option value="EFECTIVO">Efectivo</option>
                    <option value="TRANSFERENCIA">Transferencia</option>
                    <option value="MIXTO">Mixto</option>
                </select>
            </div>
            <div class="flex items-end justify-end text-gray-500 dark:text-gray-400">
                <span>Seleccionados: <strong>{{ count($selectedRecibos) }}</strong> de {{ $recibos->total() }}</span>
            </div>
        </div>
    </div>

    <!-- Tabla de Recibos de Nómina -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th scope="col" class="p-3 w-4 text-center">
                            <input type="checkbox" wire:model.live="selectAll"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                        </th>
                        <th scope="col" class="px-3 py-3 w-16 text-center">ID</th>
                        <th scope="col" class="px-3 py-3">Empleado</th>
                        <th scope="col" class="px-3 py-3">Tipo Recibo</th>
                        <th scope="col" class="px-3 py-3 text-right">Percepciones</th>
                        <th scope="col" class="px-3 py-3 text-right">Deducciones</th>
                        <th scope="col" class="px-3 py-3 text-right">Neto a Pagar</th>
                        <th scope="col" class="px-3 py-3 text-center">Método</th>
                        <th scope="col" class="px-3 py-3 text-center">Estado</th>
                        <th scope="col" class="px-3 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($recibos as $recibo)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                            <!-- Checkbox para selección rápida -->
                            <td class="p-3 text-center">
                                <input type="checkbox" wire:model.live="selectedRecibos" value="{{ $recibo->id }}"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                            </td>

                            <!-- ID Empleado -->
                            <td class="px-3 py-3 text-center font-bold text-gray-900 dark:text-white">
                                #{{ $recibo->id_empleado_snapshot }}
                            </td>

                            <!-- Nombre y Snapshot Área/Puesto -->
                            <td class="px-3 py-3">
                                <div class="font-semibold text-gray-900 dark:text-white">
                                    {{ $recibo->nombre_empleado_snapshot }}
                                </div>
                                <div class="text-[11px] text-gray-500 dark:text-gray-400">
                                    {{ $recibo->puesto_snapshot ?: 'Sin puesto' }} &bull; {{ $recibo->area_snapshot ?: 'Sin área' }}
                                </div>
                            </td>

                            <!-- Tipo de Recibo -->
                            <td class="px-3 py-3">
                                @if ($recibo->tipo_recibo === 'NOMINA')
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-900/40 dark:text-blue-300 dark:border-blue-800">
                                        Nómina
                                    </span>
                                @elseif ($recibo->tipo_recibo === 'AGUINALDO')
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-900/40 dark:text-amber-300">
                                        Aguinaldo
                                    </span>
                                @elseif ($recibo->tipo_recibo === 'FINIQUITO')
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-900/40 dark:text-rose-300">
                                        Finiquito
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded bg-gray-100 text-gray-700 border border-gray-200">
                                        {{ $recibo->tipo_recibo }}
                                    </span>
                                @endif
                            </td>

                            <!-- Total Percepciones (Fiscal + Efectivo + Extras) -->
                            <td class="px-3 py-3 text-right">
                                <div class="font-medium text-gray-900 dark:text-white">
                                    ${{ number_format($recibo->total_percepciones, 2) }}
                                </div>
                                <div class="text-[10px] text-gray-400">
                                    Fisc: ${{ number_format($recibo->nomina_fiscal, 0) }} | Efec: ${{ number_format($recibo->diferencia_efectivo, 0) }}
                                </div>
                            </td>

                            <!-- Total Deducciones -->
                            <td class="px-3 py-3 text-right">
                                <div class="font-medium text-red-600 dark:text-red-400">
                                    ${{ number_format($recibo->total_deducciones, 2) }}
                                </div>
                                @if ($recibo->descuento_adelanto > 0)
                                    <div class="text-[10px] text-amber-600 dark:text-amber-400">
                                        Adel: -${{ number_format($recibo->descuento_adelanto, 0) }}
                                    </div>
                                @endif
                            </td>

                            <!-- Neto a Pagar -->
                            <td class="px-3 py-3 text-right">
                                <div class="font-bold text-base text-gray-900 dark:text-white">
                                    ${{ number_format($recibo->neto_pagar, 2) }}
                                </div>
                            </td>

                            <!-- Método de Pago -->
                            <td class="px-3 py-3 text-center">
                                <span class="text-[11px] font-semibold text-gray-600 dark:text-gray-300 uppercase">
                                    {{ $recibo->metodo_pago }}
                                </span>
                            </td>

                            <!-- Estado de Pago -->
                            <td class="px-3 py-3 text-center">
                                <button wire:click="toggleEstadoPago({{ $recibo->id }})" title="Clic para alternar estado"
                                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold cursor-pointer transition {{ $recibo->estado_pago === 'PAGADO' ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $recibo->estado_pago === 'PAGADO' ? 'bg-green-600' : 'bg-amber-500' }}"></span>
                                    {{ $recibo->estado_pago }}
                                </button>
                            </td>

                            <!-- Acciones -->
                            <td class="px-3 py-3 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <!-- Desplegar / Acordeón -->
                                    <button wire:click="toggleExpand({{ $recibo->id }})"
                                        class="p-1.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg dark:hover:bg-gray-700 transition"
                                        title="Ver desglose detallado">
                                        <svg class="w-4 h-4 transform {{ in_array($recibo->id, $expandedRecibos) ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>

                                    <!-- Imprimir PDF Individual (Media Carta) -->
                                    <a href="{{ route('nomina.pdf.individual', $recibo->id) }}" target="_blank"
                                        class="p-1.5 text-purple-600 hover:text-purple-800 hover:bg-purple-50 rounded-lg dark:text-purple-400 dark:hover:bg-gray-700 transition"
                                        title="Imprimir Recibo Individual en Media Carta">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                        </svg>
                                    </a>

                                    <!-- Editar Recibo -->
                                    <button wire:click="editRecibo({{ $recibo->id }})"
                                        class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg dark:text-blue-400 dark:hover:bg-gray-700 transition"
                                        title="Editar recibo">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    <!-- Eliminar Recibo -->
                                    <button wire:click="confirmDeleteRecibo({{ $recibo->id }})"
                                        class="p-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg dark:text-red-400 dark:hover:bg-gray-700 transition"
                                        title="Eliminar recibo">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Fila Desplegable con Detalles de Percepciones y Deducciones -->
                        @if (in_array($recibo->id, $expandedRecibos))
                            <tr class="bg-gray-50 dark:bg-gray-750/70">
                                <td colspan="10" class="px-6 py-4">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                                        
                                        <!-- Desglose Percepciones -->
                                        <div class="p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 space-y-1">
                                            <span class="font-bold text-gray-700 dark:text-gray-300 uppercase block mb-1">Percepciones Detalladas</span>
                                            <div class="flex justify-between"><span>Sueldo Fiscal:</span> <strong>${{ number_format($recibo->nomina_fiscal, 2) }}</strong></div>
                                            <div class="flex justify-between"><span>Dif. Efectivo:</span> <strong>${{ number_format($recibo->diferencia_efectivo, 2) }}</strong></div>
                                            <div class="flex justify-between"><span>Horas Extras:</span> <strong>${{ number_format($recibo->horas_extras, 2) }}</strong></div>
                                            <div class="flex justify-between"><span>Bonos / Otros:</span> <strong>${{ number_format($recibo->bonos_otros, 2) }}</strong></div>
                                            @if ($recibo->monto_aguinaldo > 0)
                                                <div class="flex justify-between text-amber-600"><span>Aguinaldo:</span> <strong>${{ number_format($recibo->monto_aguinaldo, 2) }}</strong></div>
                                            @endif
                                            @if ($recibo->monto_vacaciones > 0 || $recibo->monto_prima_vacacional > 0)
                                                <div class="flex justify-between"><span>Vacaciones / Prima:</span> <strong>${{ number_format($recibo->monto_vacaciones + $recibo->monto_prima_vacacional, 2) }}</strong></div>
                                            @endif
                                            <div class="flex justify-between pt-1 border-t font-bold text-gray-900 dark:text-white"><span>Total:</span> <span>${{ number_format($recibo->total_percepciones, 2) }}</span></div>
                                        </div>

                                        <!-- Desglose Deducciones -->
                                        <div class="p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 space-y-1">
                                            <span class="font-bold text-red-600 dark:text-red-400 uppercase block mb-1">Deducciones Detalladas</span>
                                            <div class="flex justify-between"><span>Amortización Adelanto:</span> <strong class="text-amber-600">-${{ number_format($recibo->descuento_adelanto, 2) }}</strong></div>
                                            <div class="flex justify-between"><span>Retención Infonavit:</span> <strong>-${{ number_format($recibo->descuento_infonavit, 2) }}</strong></div>
                                            <div class="flex justify-between"><span>Otras Deducciones:</span> <strong>-${{ number_format($recibo->otros_descuentos, 2) }}</strong></div>
                                            <div class="flex justify-between pt-1 border-t font-bold text-red-600"><span>Total:</span> <span>-${{ number_format($recibo->total_deducciones, 2) }}</span></div>
                                        </div>

                                        <!-- Observaciones y Fechas -->
                                        <div class="p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 space-y-1.5">
                                            <span class="font-bold text-gray-700 dark:text-gray-300 uppercase block">Observaciones</span>
                                            <p class="italic text-gray-600 dark:text-gray-300">
                                                {{ $recibo->observaciones ?: 'Sin observaciones capturadas.' }}
                                            </p>
                                            <div class="pt-2 border-t text-[11px] text-gray-400">
                                                <span>Folio: {{ $recibo->folio }}</span><br>
                                                <span>Período: {{ $recibo->fecha_inicio?->format('d/m/Y') }} al {{ $recibo->fecha_fin?->format('d/m/Y') }}</span>
                                                @if ($recibo->fecha_pago_real)
                                                    <br><span class="text-green-600 font-semibold">Pagado el: {{ $recibo->fecha_pago_real->format('d/m/Y') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-base font-semibold">No hay recibos en este período</p>
                                    <p class="text-xs text-gray-400 mt-1">Puedes importar desde una plantilla de Excel o agregar recibos manualmente.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($recibos->hasPages())
            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                {{ $recibos->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL PERÍODO (Crear / Editar) -->
    @if ($showPeriodoModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ $isEditingPeriodo ? 'Editar Período de Nómina' : 'Crear Nuevo Período' }}
                    </h3>
                    <button wire:click="$set('showPeriodoModal', false)" class="text-gray-400 hover:text-gray-600">
                        &times;
                    </button>
                </div>

                <form wire:submit="savePeriodo" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Nombre / Título del Período *</label>
                        <input type="text" wire:model="periodoNombre"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            placeholder="Ej. Nómina 1ra Quincena Octubre 2026">
                        @error('periodoNombre') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Tipo de Período *</label>
                            <select wire:model.live="periodoTipo"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="DIARIO">DIARIO (1 Día - Meseros/Cocineros)</option>
                                <option value="SEMANAL">SEMANAL</option>
                                <option value="QUINCENAL">QUINCENAL</option>
                                <option value="MENSUAL">MENSUAL</option>
                                <option value="AGUINALDO">AGUINALDO</option>
                                <option value="FINIQUITO">FINIQUITO</option>
                                <option value="EXTRAORDINARIO">EXTRAORDINARIO</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Estado</label>
                            <select wire:model="periodoEstado"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="BORRADOR">Borrador</option>
                                <option value="EN_PROCESO">En Proceso</option>
                                <option value="DISPERSADO">Dispersado</option>
                                <option value="CERRADO">Cerrado</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Fecha Inicio *</label>
                            <input type="date" wire:model.live="periodoFechaInicio"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @error('periodoFechaInicio') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Fecha Fin *</label>
                            <input type="date" wire:model="periodoFechaFin"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @error('periodoFechaFin') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Fecha Estimada de Pago</label>
                        <input type="date" wire:model="periodoFechaPago"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" wire:click="$set('showPeriodoModal', false)"
                            class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                            {{ $isEditingPeriodo ? 'Guardar Cambios' : 'Crear Período' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL RECIBO (Crear / Editar) -->
    @if ($showReciboModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="relative w-full max-w-3xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden my-6">
                
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ $isEditingRecibo ? "Editar Recibo {$form->recibo?->folio}" : 'Nuevo Recibo de Nómina' }}
                    </h3>
                    <button wire:click="$set('showReciboModal', false)" class="text-gray-400 hover:text-gray-600">
                        &times;
                    </button>
                </div>

                <form wire:submit="saveRecibo" class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
                    
                    <!-- Empleado y Tipo de Recibo -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Empleado *</label>
                            @if ($isEditingRecibo)
                                <input type="text" disabled value="{{ $form->recibo?->nombre_empleado_snapshot }}"
                                    class="bg-gray-100 border border-gray-300 text-gray-700 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                            @else
                                <select wire:model.live="form.id_empleado" wire:change="onEmpleadoSelected($event.target.value)"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Selecciona un empleado...</option>
                                    @foreach ($empleadosActivos as $emp)
                                        <option value="{{ $emp->id }}">
                                            #{{ $emp->id }} - {{ $emp->nombre_completo }} [{{ $emp->tipo_empleado }}]
                                        </option>
                                    @endforeach
                                </select>
                                @error('form.id_empleado') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            @endif
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Tipo de Recibo *</label>
                            <select wire:model.live="form.tipo_recibo"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="NOMINA">NOMINA</option>
                                <option value="AGUINALDO">AGUINALDO</option>
                                <option value="FINIQUITO">FINIQUITO</option>
                                <option value="EXTRAORDINARIO">EXTRAORDINARIO</option>
                            </select>
                        </div>
                    </div>

                    <!-- Si el tipo es FINIQUITO, confirmar si se da de baja al empleado -->
                    @if ($form->tipo_recibo === 'FINIQUITO' && !$isEditingRecibo)
                        <div class="p-3 bg-rose-50 border border-rose-200 rounded-lg dark:bg-rose-950/40 dark:border-rose-800">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model="darDeBajaAlFiniquitar" class="w-4 h-4 text-rose-600 rounded">
                                <span class="text-sm font-semibold text-rose-900 dark:text-rose-200">
                                    ¿Deseas dar de baja automáticamente al empleado en el sistema al emitir este finiquito?
                                </span>
                            </label>
                        </div>
                    @endif

                    <!-- Fechas del Recibo -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Fecha Inicio *</label>
                            <input type="date" wire:model="form.fecha_inicio"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @error('form.fecha_inicio') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Fecha Fin *</label>
                            <input type="date" wire:model="form.fecha_fin"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @error('form.fecha_fin') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Fecha de Pago</label>
                            <input type="date" wire:model="form.fecha_pago"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>

                    <!-- Percepciones -->
                    <div class="p-4 bg-blue-50/50 dark:bg-gray-750 rounded-xl border border-blue-100 dark:border-gray-700 space-y-3">
                        <span class="block text-xs font-bold text-blue-900 dark:text-blue-300 uppercase">Percepciones Económicas</span>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Nómina Fiscal</label>
                                <input type="number" step="0.01" wire:model.live="form.nomina_fiscal"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg p-2 w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Dif. Efectivo</label>
                                <input type="number" step="0.01" wire:model.live="form.diferencia_efectivo"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg p-2 w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Horas Extras</label>
                                <input type="number" step="0.01" wire:model.live="form.horas_extras"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg p-2 w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Bonos / Otros</label>
                                <input type="number" step="0.01" wire:model.live="form.bonos_otros"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg p-2 w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>

                        <!-- Aguinaldo y Vacaciones si aplica -->
                        @if ($form->tipo_recibo === 'AGUINALDO' || $form->tipo_recibo === 'FINIQUITO')
                            <div class="grid grid-cols-3 gap-3 pt-2 border-t border-blue-200 dark:border-gray-600">
                                <div>
                                    <label class="block text-xs text-amber-700 dark:text-amber-400 mb-1 font-semibold">Monto Aguinaldo</label>
                                    <input type="number" step="0.01" wire:model.live="form.monto_aguinaldo"
                                        class="bg-white border border-amber-300 text-gray-900 text-sm rounded-lg p-2 w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Vacaciones</label>
                                    <input type="number" step="0.01" wire:model.live="form.monto_vacaciones"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg p-2 w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Prima Vacacional</label>
                                    <input type="number" step="0.01" wire:model.live="form.monto_prima_vacacional"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg p-2 w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Deducciones -->
                    <div class="p-4 bg-red-50/50 dark:bg-gray-750 rounded-xl border border-red-100 dark:border-gray-700 space-y-3">
                        <span class="block text-xs font-bold text-red-900 dark:text-red-300 uppercase">Deducciones / Descuentos</span>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Amortización Adelanto Efectivo</label>
                                <input type="number" step="0.01" wire:model.live="form.descuento_adelanto"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg p-2 w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="0.00">
                                <span class="text-[10px] text-gray-400">Se abonará a su cuenta contable</span>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Infonavit</label>
                                <input type="number" step="0.01" wire:model.live="form.descuento_infonavit"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg p-2 w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Otros Descuentos</label>
                                <input type="number" step="0.01" wire:model.live="form.otros_descuentos"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg p-2 w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Método de Pago y Estatus -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Método de Pago</label>
                            <select wire:model="form.metodo_pago"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="EFECTIVO">EFECTIVO (Predeterminado)</option>
                                <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                                <option value="MIXTO">MIXTO</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Estado de Pago</label>
                            <select wire:model="form.estado_pago"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="PENDIENTE">PENDIENTE</option>
                                <option value="PAGADO">PAGADO</option>
                                <option value="PARCIAL">PARCIAL</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Fecha de Pago Real</label>
                            <input type="date" wire:model="form.fecha_pago_real"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>

                    <!-- Observaciones (Requisito 2.1.1) -->
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Observaciones</label>
                        <textarea wire:model="form.observaciones" rows="2"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            placeholder="Detalles adicionales, número de turno o evento, etc..."></textarea>
                    </div>

                    <!-- Resumen del Neto Calculado -->
                    <div class="flex items-center justify-between p-4 bg-gray-100 dark:bg-gray-700 rounded-xl">
                        <span class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase">Neto Calculado:</span>
                        <span class="text-xl font-black text-blue-600 dark:text-blue-400">
                            ${{ number_format($form->calcularNeto(), 2) }}
                        </span>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" wire:click="$set('showReciboModal', false)"
                            class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                            {{ $isEditingRecibo ? 'Guardar Recibo' : 'Emitir Recibo' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL IMPORTAR EXCEL -->
    @if ($showImportModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        Importación Masiva desde Excel
                    </h3>
                    <button wire:click="$set('showImportModal', false)" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>

                <div class="space-y-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Sube el archivo Excel generado previamente con la plantilla de nómina. El sistema asociará los recibos automáticamente al período activo: <strong>{{ $selectedPeriodo?->nombre }}</strong>.
                    </p>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Selecciona archivo (.xlsx, .xls)</label>
                        <input type="file" wire:model="archivoExcel" accept=".xlsx,.xls,.csv"
                            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600">
                        @error('archivoExcel') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    @if (!empty($importErrors))
                        <div class="p-3 bg-red-50 rounded-lg border border-red-200 text-xs text-red-700 max-h-40 overflow-y-auto">
                            <span class="font-bold block mb-1">Advertencias durante la importación:</span>
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach ($importErrors as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="flex justify-end gap-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" wire:click="$set('showImportModal', false)"
                            class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                            Cerrar
                        </button>
                        <button wire:click="importarExcel" type="button"
                            class="px-5 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 flex items-center gap-2">
                            <span wire:loading wire:target="importarExcel, archivoExcel" class="inline-block animate-spin">&#9696;</span>
                            Procesar Importación
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL CONFIRMAR ELIMINAR PERÍODO -->
    @if ($showDeletePeriodoModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 text-center">
                <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 mx-auto flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">¿Eliminar Período Completo?</h3>
                <p class="text-sm text-gray-500 mb-6">Esta acción eliminará el período <strong>{{ $selectedPeriodo?->nombre }}</strong> y todos sus recibos asociados de forma irreversible.</p>
                <div class="flex justify-center gap-3">
                    <button wire:click="$set('showDeletePeriodoModal', false)" class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</button>
                    <button wire:click="deletePeriodo" class="px-5 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700">Sí, Eliminar Todo</button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL CONFIRMAR ELIMINAR RECIBO -->
    @if ($showDeleteReciboModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 text-center">
                <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 mx-auto flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">¿Eliminar Recibo?</h3>
                <p class="text-sm text-gray-500 mb-6">¿Estás seguro de eliminar este recibo? Los totales del período se recalcularán automáticamente.</p>
                <div class="flex justify-center gap-3">
                    <button wire:click="$set('showDeleteReciboModal', false)" class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</button>
                    <button wire:click="deleteRecibo" class="px-5 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700">Eliminar</button>
                </div>
            </div>
        </div>
    @endif
</div>

