<div>
    <div class="space-y-6">
    <!-- Selector y Tarjetas de Período Activo -->
    <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex flex-col xl:flex-row xl:items-end xl:justify-between gap-4">
            
            <!-- Controles de Filtros y Selección de Período -->
            <div class="flex flex-wrap items-end gap-3 flex-1 p-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-750/30">
                <!-- Filtro Año -->
                <div class="w-28 flex-shrink-0">
                    <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Año</label>
                    <select wire:model.live="filtroAnioPeriodo"
                        class="bg-white border border-gray-300 text-gray-900 text-sm font-bold rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-pointer">
                        @foreach ($aniosDisponibles as $anio)
                            <option value="{{ $anio }}">{{ $anio }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro Tipo de Nómina -->
                <div class="w-40 flex-shrink-0">
                    <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Tipo</label>
                    <select wire:model.live="filtroTipoPeriodo"
                        class="bg-white border border-gray-300 text-gray-900 text-sm font-semibold rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-pointer">
                        <option value="TODOS">TODOS</option>
                        <option value="QUINCENAL">QUINCENAL</option>
                        <option value="SEMANAL">SEMANAL</option>
                        <option value="MENSUAL">MENSUAL</option>
                        <option value="DIARIO">DIARIO</option>
                        <option value="AGUINALDO">AGUINALDO</option>
                        <option value="FINIQUITO">FINIQUITO</option>
                        <option value="EXTRAORDINARIO">EXTRAORDINARIO</option>
                    </select>
                </div>

                <!-- Selector de Período Filtrado -->
                <div class="flex-1 min-w-[260px]">
                    <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">
                        Período de Nómina ({{ count($periodos) }})
                    </label>
                    <select wire:model.live="selectedPeriodoId" wire:key="periodo-select-{{ $filtroAnioPeriodo }}-{{ $filtroTipoPeriodo }}-{{ $selectedPeriodoId }}"
                        class="bg-white border border-gray-300 text-gray-900 text-sm font-bold rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-pointer">
                        <option value="">
                            -- Selecciona un período --
                        </option>
                        @forelse ($periodos as $p)
                            <option value="{{ $p->id }}">
                                {{ $p->nombre }} ({{ $p->fecha_inicio ? $p->fecha_inicio->format('d/m/Y') : '' }} - {{ $p->fecha_fin ? $p->fecha_fin->format('d/m/Y') : '' }}) [{{ $p->tipo_periodo }}]
                            </option>
                        @empty
                            <option value="" disabled>No hay períodos registrados para los filtros seleccionados</option>
                        @endforelse
                    </select>
                </div>
            </div>

            <!-- Botones de Gestión de Período -->
            <div class="flex flex-wrap items-center gap-2 flex-shrink-0 self-end mb-3">
                <button wire:click="createPeriodo" type="button"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2.5 text-xs font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Nuevo Período</span>
                </button>

                @if ($selectedPeriodo)
                    <button wire:click="editPeriodo({{ $selectedPeriodo->id }})" type="button"
                        class="inline-flex items-center justify-center p-2.5 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 shadow-sm transition"
                        title="Modificar fechas, nombre o estado de este período" aria-label="Editar período">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                    </button>

                    <button wire:click="recalcularTotalesPeriodo" type="button"
                        class="inline-flex items-center justify-center p-2.5 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 shadow-sm transition"
                        title="Actualizar sumatorias del período" aria-label="Recalcular período">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>

                    @if ($selectedPeriodo->estado !== 'CERRADO')
                        <button wire:click="confirmDeletePeriodo" type="button"
                            class="inline-flex items-center justify-center p-2.5 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 shadow-sm transition"
                            title="Eliminar este período" aria-label="Eliminar período">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    @endif
                @endif
            </div>
        </div>

        @if ($selectedPeriodo && $selectedPeriodo->estado === 'CERRADO')
            <!-- Banner de Alerta Período Cerrado -->
            <div class="mt-4 p-3 bg-rose-50 border border-rose-200 dark:bg-rose-950/30 dark:border-rose-900 rounded-xl flex items-center justify-between text-xs text-rose-800 dark:text-rose-200">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <div>
                        <strong class="font-bold">Período Cerrado:</strong> La creación, edición y eliminación de recibos se encuentra bloqueada. Sólo se permite la consulta e impresión. Puedes reabrir el período modificando su estado con el botón de edición.
                    </div>
                </div>
                <button wire:click="editPeriodo({{ $selectedPeriodo->id }})" class="underline font-semibold text-rose-700 dark:text-rose-300 hover:text-rose-900 whitespace-nowrap ms-3">
                    Reabrir Período
                </button>
            </div>
        @endif

        <!-- Indicadores / Métricas del Período -->
        @if ($selectedPeriodo)
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mt-5 pt-4 border-t border-gray-100 dark:border-gray-700">
                <div class="bg-gray-50 dark:bg-gray-750 p-3 rounded-lg border border-gray-100 dark:border-gray-700">
                    <span class="text-[11px] font-semibold text-gray-500 uppercase">Tipo & Estado</span>
                    <div class="flex items-center gap-1.5 mt-1">
                        <span class="px-2 py-0.5 text-xs font-bold rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            {{ $selectedPeriodo->tipo_periodo }}
                        </span>
                        @if ($selectedPeriodo->estado === 'CERRADO')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-bold rounded-full bg-rose-100 text-rose-800 dark:bg-rose-900/50 dark:text-rose-200 border border-rose-300 dark:border-rose-800">
                                <svg class="w-3 h-3 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                CERRADO
                            </span>
                        @else
                            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                                {{ $selectedPeriodo->estado }}
                            </span>
                        @endif
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

            <!-- Banner de Auditoría de Saldos Pendientes -->
            @php
                $esDiarioOExtraordinario = in_array(strtoupper($selectedPeriodo?->tipo_periodo ?? ''), ['DIARIO', 'EXTRAORDINARIO']);
            @endphp
            @if ($totalPendientePeriodo > 0 && !$esDiarioOExtraordinario)
                <div class="mt-4 p-3.5 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 dark:from-amber-950/40 dark:to-orange-950/30 dark:border-amber-800/60 rounded-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-amber-100 text-amber-700 dark:bg-amber-900/60 dark:text-amber-300 flex items-center justify-center flex-shrink-0 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-xs font-bold text-amber-900 dark:text-amber-200 uppercase tracking-wide">
                                    Nómina Incompleta · Saldos Pendientes
                                </span>
                                <span class="px-2 py-0.5 text-[10px] font-black rounded-full bg-amber-200 text-amber-900 dark:bg-amber-900 dark:text-amber-200">
                                    {{ $conteoIncompletosPeriodo }} empleados pendientes
                                </span>
                            </div>
                            <p class="text-xs text-amber-800 dark:text-amber-300 mt-0.5">
                                Faltan <strong class="font-bold text-amber-950 dark:text-amber-100">${{ number_format($totalPendientePeriodo, 2) }}</strong> por dispersar del salario base estimado (${{ number_format($totalPresupuestoPeriodo, 2) }}).
                            </p>
                        </div>
                    </div>
                    <button type="button" wire:click="openAuditoriaModal"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-lg shadow-sm transition whitespace-nowrap self-stretch sm:self-auto justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        Ver saldos
                    </button>
                </div>
            @elseif ($totalPresupuestoPeriodo > 0 && !$esDiarioOExtraordinario)
                <div class="mt-4 p-3 bg-emerald-50 border border-emerald-200 dark:bg-emerald-950/30 dark:border-emerald-800/60 rounded-xl flex items-center justify-between text-xs text-emerald-800 dark:text-emerald-300">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span><strong>Nómina Completa:</strong> El 100% del salario base estimado ha sido cubierto en este período.</span>
                    </div>
                    <button type="button" wire:click="openAuditoriaModal" class="text-xs font-semibold underline text-emerald-700 dark:text-emerald-400 hover:text-emerald-900">
                        Ver auditoría
                    </button>
                </div>
            @endif
        @else
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 text-center py-4 bg-gray-50/50 rounded-lg dark:bg-gray-750/30">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Selecciona un período de nómina en los filtros para consultar sus indicadores, montos y balance.
                </p>
            </div>
        @endif
    </div>

    <!-- Barra de Herramientas de Recibos (Importar, Exportar, Multi-Impresión) -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            
            <!-- Acciones Rápidas -->
            <div class="flex flex-wrap items-center gap-2">
                @if ($selectedPeriodo?->estado !== 'CERRADO')
                    <!-- Botón Nuevo Recibo Individual -->
                    <button wire:click="createRecibo" type="button"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Nuevo Recibo
                    </button>

                    <!-- Descargar Plantilla Excel -->
                    @if ($selectedPeriodoId)
                        @if ($esDiarioOExtraordinario)
                            <a href="{{ route('nomina.excel.plantilla', ['id_periodo' => $selectedPeriodoId]) }}" target="_blank"
                                class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-emerald-800 bg-emerald-50 border border-emerald-300 rounded-lg hover:bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-800 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                <span>Plantilla Excel</span>
                            </a>
                        @else
                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                <button @click="open = !open" @click.away="open = false" type="button"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-emerald-800 bg-emerald-50 border border-emerald-300 rounded-lg hover:bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-800 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    <span>Plantilla Excel</span>
                                    <svg class="w-3.5 h-3.5 ms-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div x-show="open" x-cloak
                                    class="absolute left-0 z-30 mt-1.5 w-64 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 py-1.5 text-xs">
                                    <a href="{{ route('nomina.excel.plantilla', ['id_periodo' => $selectedPeriodoId]) }}" target="_blank"
                                        @click="open = false"
                                        class="flex items-start gap-2.5 px-3.5 py-2 hover:bg-gray-50 dark:hover:bg-gray-750 text-gray-700 dark:text-gray-200">
                                        <svg class="w-4 h-4 text-emerald-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <div>
                                            <span class="font-bold block">Plantilla Base (Completa)</span>
                                            <span class="text-[11px] text-gray-400">Todos los empleados con su salario habitual</span>
                                        </div>
                                    </a>
                                    <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                                    <a href="{{ route('nomina.excel.plantilla', ['id_periodo' => $selectedPeriodoId, 'solo_incompletos' => 1]) }}" target="_blank"
                                        @click="open = false"
                                        class="flex items-start gap-2.5 px-3.5 py-2 hover:bg-amber-50 dark:hover:bg-amber-950/30 text-amber-900 dark:text-amber-300">
                                        <svg class="w-4 h-4 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        <div>
                                            <span class="font-bold block">Plantilla Saldos Pendientes</span>
                                            <span class="text-[11px] text-gray-500 dark:text-gray-400">Solo empleados incompletos con importe faltante</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endif
                    @else
                        <button type="button" disabled title="Selecciona un período primero"
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed dark:bg-gray-800 dark:text-gray-500 dark:border-gray-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Plantilla Excel
                        </button>
                    @endif

                    <!-- Importar Plantilla Excel -->
                    <button wire:click="openImportModal" type="button"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold {{ $selectedPeriodoId ? 'text-gray-700 bg-gray-100 border border-gray-300 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600' : 'text-gray-400 bg-gray-100 border border-gray-200 cursor-not-allowed dark:bg-gray-800 dark:text-gray-500 dark:border-gray-700' }} rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12"/>
                        </svg>
                        Importar Excel
                    </button>
                @endif

                <!-- Botón Reporte Contable de Nómina -->
                <button wire:click="openReporteModal" type="button"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-blue-800 bg-blue-50 border border-blue-300 rounded-lg hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Reporte Contable
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
                <input wire:model.live.debounce.350ms="reciboSearch" type="text"
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
                        <th scope="col" class="px-3 py-3">Empleado</th>
                        <th scope="col" class="px-3 py-3">Tipo & Folio</th>
                        <th scope="col" class="px-3 py-3 text-right">Percepciones</th>
                        <th scope="col" class="px-3 py-3 text-right">Deducciones</th>
                        <th scope="col" class="px-3 py-3 text-right">Neto a Pagar</th>
                        <th scope="col" class="px-3 py-3 text-center">Método</th>
                        <th scope="col" class="px-3 py-3 text-center">
                            <div class="inline-flex items-center justify-center gap-1.5">
                                <span>Estado</span>
                                @if ($selectedPeriodo && $selectedPeriodo->estado !== 'CERRADO')
                                    <button type="button"
                                        wire:click="marcarRecibosVisiblesComoPagados"
                                        wire:confirm="¿Deseas marcar como PAGADOS todos los recibos no pagados (PENDIENTES y PARCIALES) que coinciden con los filtros visibles actuales?"
                                        title="Marcar todos los recibos no pagados visibles como PAGADO"
                                        class="p-1 rounded text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-3 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($recibos as $recibo)
                        <tr wire:key="recibo-row-{{ $recibo->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                            <!-- Checkbox para selección rápida -->
                            <td class="p-3 text-center">
                                <input type="checkbox" wire:key="recibo-check-{{ $recibo->id }}" wire:model.live="selectedRecibos" value="{{ $recibo->id }}"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                            </td>

                            <!-- Nombre con ID y Snapshot Área/Puesto -->
                            <td class="px-3 py-3">
                                <div class="font-semibold text-gray-900 dark:text-white">
                                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400 mr-1">#{{ $recibo->id_empleado_snapshot }}</span>
                                    <span>{{ $recibo->nombre_empleado_snapshot }}</span>
                                </div>
                                <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">
                                    {{ $recibo->puesto_snapshot ?: 'Sin puesto' }} &bull; {{ $recibo->area_snapshot ?: 'Sin área' }}
                                </div>
                            </td>

                            <!-- Tipo de Recibo y Folio Consecutivo -->
                            <td class="px-3 py-3">
                                <div class="flex flex-col items-start gap-1">
                                    @if ($recibo->tipo_recibo === 'NOMINA')
                                        <span class="px-2 py-0.5 text-[11px] font-semibold rounded bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-900/40 dark:text-blue-300 dark:border-blue-800">
                                            Nómina
                                        </span>
                                    @elseif ($recibo->tipo_recibo === 'AGUINALDO')
                                        <span class="px-2 py-0.5 text-[11px] font-semibold rounded bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-900/40 dark:text-amber-300">
                                            Aguinaldo
                                        </span>
                                    @elseif ($recibo->tipo_recibo === 'FINIQUITO')
                                        <span class="px-2 py-0.5 text-[11px] font-semibold rounded bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-900/40 dark:text-rose-300">
                                            Finiquito
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 text-[11px] font-semibold rounded bg-gray-100 text-gray-700 border border-gray-200">
                                            {{ $recibo->tipo_recibo }}
                                        </span>
                                    @endif

                                    <span class="px-1.5 py-0.5 text-[10px] font-mono font-bold bg-gray-100 text-gray-700 rounded dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600" title="Folio Consecutivo del Empleado">
                                        {{ $recibo->folio }}
                                    </span>
                                </div>
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

                                    @if ($selectedPeriodo?->estado !== 'CERRADO')
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
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Fila Desplegable con Detalles de Percepciones y Deducciones -->
                        @if (in_array($recibo->id, $expandedRecibos))
                            <tr wire:key="recibo-detail-{{ $recibo->id }}" class="bg-gray-50 dark:bg-gray-750/70">
                                <td colspan="9" class="px-6 py-4">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                                        
                                        <!-- Desglose Percepciones -->
                                        <div class="p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 space-y-1">
                                            <span class="font-bold text-gray-700 dark:text-gray-300 uppercase block mb-1">Percepciones Detalladas</span>
                                            <div class="flex justify-between"><span>Sueldo Fiscal:</span> <strong>${{ number_format($recibo->nomina_fiscal, 2) }}</strong></div>
                                            <div class="flex justify-between"><span>Dif. Efectivo:</span> <strong>${{ number_format($recibo->diferencia_efectivo, 2) }}</strong></div>
                                            <div class="flex justify-between"><span>Horas Extras:</span> <strong>${{ number_format($recibo->horas_extras, 2) }}</strong></div>
                                            <div class="flex justify-between"><span>Bonos / Otros:</span> <strong>${{ number_format($recibo->bonos_otros, 2) }}</strong></div>
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
                        <tr wire:key="recibo-empty">
                            <td colspan="9" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    @if ($selectedPeriodo)
                                        <p class="text-base font-semibold">No hay recibos en este período</p>
                                        <p class="text-xs text-gray-400 mt-1">Puedes importar desde una plantilla de Excel o agregar recibos manualmente.</p>
                                    @else
                                        <p class="text-base font-semibold text-gray-700 dark:text-gray-300">Ningún período seleccionado</p>
                                        <p class="text-xs text-gray-400 mt-1">Por favor selecciona un período en la parte superior o crea uno nuevo para consultar o registrar recibos.</p>
                                    @endif
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
    </div>

    <!-- MODAL PERÍODO (Crear / Editar) -->
    @if ($showPeriodoModal)
        <div class="fixed inset-0 z-50 overflow-y-auto m-0" role="dialog" aria-modal="true">
            <!-- Backdrop fijo (cubre el 100% de la pantalla) -->
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity m-0"
                 wire:click="$set('showPeriodoModal', false)"
                 aria-hidden="true"></div>

            <!-- Contenedor centrado para scroll -->
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 m-0"
                 wire:click.self="$set('showPeriodoModal', false)">
                <div class="relative w-full max-w-lg my-6 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 p-6 text-left overflow-hidden transform transition-all"
                     @click.stop>
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $isEditingPeriodo ? 'Editar Período de Nómina' : 'Crear Nuevo Período' }}
                        </h3>
                        <button wire:click="$set('showPeriodoModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xl font-bold">
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
                                    <option value="APROBADO">Aprobado</option>
                                    <option value="DISPERSADO">Dispersado</option>
                                    <option value="PAGADO">Pagado</option>
                                    <option value="CERRADO">Cerrado</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Fecha Inicio *</label>
                                <input type="date" wire:model.live.debounce.350ms="periodoFechaInicio"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('periodoFechaInicio') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Fecha Fin *</label>
                                <input type="date" wire:model.live.debounce.350ms="periodoFechaFin"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('periodoFechaFin') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Fecha Estimada de Pago</label>
                            <input type="date" wire:model.live.debounce.350ms="periodoFechaPago"
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
        </div>
    @endif

    <!-- MODAL RECIBO (Crear / Editar) -->
    @if ($showReciboModal)
        <div class="fixed inset-0 z-50 overflow-y-auto m-0" role="dialog" aria-modal="true">
            <!-- Backdrop fijo (cubre el 100% de la pantalla) -->
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity m-0"
                 @if (!$enSecuencia) wire:click="$set('showReciboModal', false)" @endif
                 aria-hidden="true"></div>

            <!-- Contenedor centrado para scroll -->
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 m-0"
                 @if (!$enSecuencia) wire:click.self="$set('showReciboModal', false)" @endif>
                <div class="relative w-full max-w-3xl my-6 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 text-left overflow-hidden transform transition-all"
                     @click.stop>
                
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        @if ($enSecuencia)
                            Recibo Complementario (Paso {{ $secuenciaIndice }} de {{ $secuenciaTotal }})
                        @else
                            {{ $isEditingRecibo ? "Editar Recibo {$form->recibo?->folio}" : 'Nuevo Recibo de Nómina' }}
                        @endif
                    </h3>
                    <button wire:click="{{ $enSecuencia ? 'cancelarSecuencia' : '$set(\'showReciboModal\', false)' }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xl font-bold">
                        &times;
                    </button>
                </div>

                @if ($enSecuencia)
                    <div class="bg-indigo-600 text-white px-6 py-2.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 shadow-inner">
                        <div class="flex items-center gap-2.5">
                            <span class="px-2 py-0.5 text-xs font-black bg-white/20 rounded text-white uppercase tracking-wider">
                                Secuencia Activa
                            </span>
                            <span class="text-xs font-medium">
                                Ajustando saldo para: <strong>#{{ $empleadoSeleccionado?->id }} {{ $empleadoSeleccionado?->nombre_completo }}</strong>
                            </span>
                        </div>
                        <div class="text-xs text-indigo-100 font-medium">
                            Faltante calculado: <strong class="text-white font-black font-mono text-sm">${{ number_format($saldoPendienteEmpleadoActual, 2) }}</strong>
                        </div>
                    </div>
                    <!-- Barra de progreso -->
                    <div class="w-full bg-indigo-900/30 h-1.5">
                        <div class="bg-amber-400 h-1.5 transition-all duration-300" style="width: {{ $secuenciaTotal > 0 ? round(($secuenciaIndice / $secuenciaTotal) * 100) : 0 }}%"></div>
                    </div>
                @endif

                <form wire:submit.prevent="saveRecibo(false)" class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
                    
                    <!-- Selección de Empleado -->
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">
                            Empleado *
                        </label>
                        @if ($isEditingRecibo)
                            <div class="flex items-center justify-between p-3 bg-gray-100 border border-gray-200 rounded-xl dark:bg-gray-700/60 dark:border-gray-600">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm bg-gray-500 text-white flex-shrink-0">
                                        {{ mb_strtoupper(mb_substr($form->recibo?->nombre_empleado_snapshot ?? 'E', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-gray-900 dark:text-white text-sm">
                                                {{ $form->recibo?->nombre_empleado_snapshot }}
                                            </span>
                                            <span class="text-xs px-1.5 py-0.5 rounded bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 font-mono">
                                                #{{ $form->recibo?->id_empleado_snapshot }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $form->recibo?->puesto_snapshot }} · {{ $form->recibo?->area_snapshot }}
                                        </p>
                                    </div>
                                </div>
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300">
                                    Fijo en edición
                                </span>
                            </div>
                        @elseif ($empleadoSeleccionado)
                            <!-- Tarjeta de Empleado Seleccionado -->
                            <div class="p-3 bg-blue-50/70 border border-blue-200 rounded-xl dark:bg-gray-750 dark:border-blue-900/50 space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm bg-blue-600 text-white shadow-sm overflow-hidden flex-shrink-0">
                                            @if ($empleadoSeleccionado->foto)
                                                <img src="{{ asset('storage/' . $empleadoSeleccionado->foto) }}" alt="{{ $empleadoSeleccionado->nombre }}" class="w-full h-full object-cover">
                                            @else
                                                {{ mb_strtoupper(mb_substr($empleadoSeleccionado->nombre, 0, 1)) }}{{ mb_strtoupper(mb_substr($empleadoSeleccionado->apellido_paterno, 0, 1)) }}
                                            @endif
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-gray-900 dark:text-white text-sm">
                                                    {{ $empleadoSeleccionado->nombre_completo }}
                                                </span>
                                                <span class="text-xs px-1.5 py-0.5 rounded bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-mono">
                                                    #{{ $empleadoSeleccionado->id }}
                                                </span>
                                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded {{ $empleadoSeleccionado->tipo_empleado === 'PLANTA' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300' }}">
                                                    {{ $empleadoSeleccionado->tipo_empleado }}
                                                </span>
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 flex items-center gap-2 flex-wrap">
                                                <span>{{ $empleadoSeleccionado->puesto?->nombre ?? 'Sin puesto' }}</span>
                                                <span>·</span>
                                                <span>{{ $empleadoSeleccionado->area?->nombre ?? 'Sin área' }}</span>
                                                <span>·</span>
                                                <span class="font-medium text-emerald-600 dark:text-emerald-400">Salario base: ${{ number_format($empleadoSeleccionado->salario_base, 2) }}</span>
                                                <span>·</span>
                                                <span class="text-gray-500">Pago: {{ $empleadoSeleccionado->tipo_pago_predeterminado ?? 'EFECTIVO' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @if (!$enSecuencia)
                                        <button type="button" wire:click="limpiarEmpleadoSeleccionado"
                                            class="text-xs px-2.5 py-1 text-gray-600 hover:text-red-600 bg-white border border-gray-300 hover:border-red-300 rounded-lg transition shadow-sm dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 flex items-center gap-1 font-medium">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            Cambiar
                                        </button>
                                    @endif
                                </div>

                                <!-- Monto Acordado del Período (Editable) -->
                                <div class="mt-2 pt-2 border-t border-blue-100 dark:border-blue-900/40 flex flex-wrap items-center justify-between gap-3 text-xs">
                                    <div class="flex items-center gap-2">
                                        <label for="monto_acordado_input" class="font-bold text-gray-700 dark:text-gray-200">
                                            Monto Total Acordado del Período:
                                        </label>
                                        <div class="relative w-36">
                                            <span class="absolute left-2.5 top-1.5 text-gray-500 font-bold">$</span>
                                            <input type="number" step="0.01" min="0" id="monto_acordado_input"
                                                wire:model.live.debounce.350ms="form.monto_total_acordado"
                                                placeholder="{{ number_format($salarioBaseEmpleadoActual, 2) }}"
                                                class="w-full pl-6 pr-2 py-1 text-xs font-mono font-bold text-gray-900 dark:text-white bg-white dark:bg-gray-700 border border-blue-300 dark:border-blue-700 rounded-lg focus:ring-1 focus:ring-blue-500">
                                        </div>
                                    </div>
                                    <div class="text-[11px] text-gray-500 dark:text-gray-400">
                                        (Presupuesto total acordado a cubrir en 1 o más abonos)
                                    </div>
                                </div>

                                @if ($recibosPreviosCountEmpleadoActual > 0)
                                    @php
                                        $montoMeta = (float) ($form->monto_total_acordado ?: $salarioBaseEmpleadoActual);
                                        $abonoActual = (float) ($form->metodo_pago === 'TRANSFERENCIA' ? $form->nomina_fiscal : $form->diferencia_efectivo);
                                        $saldoRestanteProyectado = max(0, round($montoMeta - ($totalEmitidoPrevioEmpleadoActual + $abonoActual), 2));
                                    @endphp
                                    <div class="p-2.5 bg-amber-50 border border-amber-300/80 rounded-lg dark:bg-amber-950/40 dark:border-amber-800 text-[11px] text-amber-900 dark:text-amber-200 space-y-1">
                                        <div class="flex items-center justify-between font-medium">
                                            <span class="flex items-center gap-1.5 font-bold">
                                                <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Abono #{{ $recibosPreviosCountEmpleadoActual + 1 }} para este período
                                            </span>
                                            <span>
                                                Ya pagado: <strong class="font-mono text-gray-900 dark:text-white">${{ number_format($totalEmitidoPrevioEmpleadoActual, 2) }}</strong> ({{ $recibosPreviosCountEmpleadoActual }} abono{{ $recibosPreviosCountEmpleadoActual > 1 ? 's' : '' }})
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between text-xs pt-1 border-t border-amber-200/60 dark:border-amber-900/50">
                                            <span>Monto Acordado: <strong class="font-mono">${{ number_format($montoMeta, 2) }}</strong></span>
                                            <span>Saldo pendiente previo: <strong class="font-mono text-amber-700 dark:text-amber-400 font-bold">${{ number_format(max(0, $montoMeta - $totalEmitidoPrevioEmpleadoActual), 2) }}</strong></span>
                                            <span>Restante tras este recibo: <strong class="font-mono {{ $saldoRestanteProyectado <= 0.01 ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-rose-600 dark:text-rose-400 font-bold' }}">${{ number_format($saldoRestanteProyectado, 2) }}</strong></span>
                                        </div>
                                    </div>
                                @endif

                                @if ($empleadoFueraDePeriodo && $motivoEmpleadoFueraDePeriodo)
                                    <div class="p-2.5 bg-amber-50 border border-amber-300 rounded-lg dark:bg-amber-950/40 dark:border-amber-800 text-[11px] text-amber-900 dark:text-amber-200 flex items-start gap-2">
                                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        <div>
                                            <span class="font-bold block mb-0.5">Atención con fechas de labores</span>
                                            <span>{{ $motivoEmpleadoFueraDePeriodo }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <!-- Buscador Autocompletado / Combobox -->
                            <div x-data="{
                                open: false,
                                activeIndex: 0,
                                getItems() {
                                    return $el.querySelectorAll('[data-employee-id]');
                                },
                                nextItem() {
                                    const items = this.getItems();
                                    if (items.length === 0) return;
                                    this.open = true;
                                    this.activeIndex = (this.activeIndex + 1) % items.length;
                                    this.scrollToActive();
                                },
                                prevItem() {
                                    const items = this.getItems();
                                    if (items.length === 0) return;
                                    this.open = true;
                                    this.activeIndex = (this.activeIndex - 1 + items.length) % items.length;
                                    this.scrollToActive();
                                },
                                selectCurrent() {
                                    if (!this.open) return;
                                    const items = this.getItems();
                                    if (items.length > 0 && items[this.activeIndex]) {
                                        const empId = items[this.activeIndex].getAttribute('data-employee-id');
                                        if (empId) {
                                            $wire.seleccionarEmpleado(parseInt(empId));
                                            this.open = false;
                                        }
                                    }
                                },
                                scrollToActive() {
                                    const items = this.getItems();
                                    if (items[this.activeIndex]) {
                                        items[this.activeIndex].scrollIntoView({ block: 'nearest' });
                                    }
                                }
                            }"
                            x-init="
                                setTimeout(() => { $refs.searchInput?.focus(); }, 120);
                                $wire.on('focus-empleado-search', () => {
                                    setTimeout(() => { $refs.searchInput?.focus(); }, 100);
                                });
                            "
                            @click.outside="open = false"
                            class="relative">
                                
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                    <input 
                                        type="text" 
                                        id="input-empleado-search"
                                        x-ref="searchInput"
                                        wire:model.live.debounce.350ms="empleadoSearch"
                                        @focus="if ($wire.empleadoSearch && $wire.empleadoSearch.trim().length > 0) open = true"
                                        @input="open = true; activeIndex = 0"
                                        @keydown.down.prevent="nextItem()"
                                        @keydown.up.prevent="prevItem()"
                                        @keydown.enter.prevent.stop="selectCurrent()"
                                        @keydown.escape.prevent.stop="open = false"
                                        placeholder="Buscar por nombre, apellido, ID (#), puesto o área..."
                                        autocomplete="off"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full pl-9 pr-8 p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
                                    
                                    @if ($empleadoSearch)
                                        <button type="button" wire:click="$set('empleadoSearch', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                            &times;
                                        </button>
                                    @endif
                                </div>

                                <!-- Dropdown Resultados (solo si se ha escrito en la barra de búsqueda) -->
                                @if (strlen(trim($empleadoSearch)) > 0)
                                    <div x-show="open" 
                                        x-cloak 
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="transform opacity-0 scale-95"
                                        x-transition:enter-end="transform opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="transform opacity-100 scale-100"
                                        x-transition:leave-end="transform opacity-0 scale-95"
                                        class="absolute left-0 right-0 z-50 mt-1 max-h-64 overflow-y-auto bg-white border border-gray-200 rounded-xl shadow-2xl dark:bg-gray-800 dark:border-gray-700 divide-y divide-gray-100 dark:divide-gray-700">
                                        
                                        @forelse ($empleadosBusqueda as $index => $emp)
                                            @php
                                                $yaEnPeriodo = in_array($emp->id, $empleadosEnPeriodoIds);
                                            @endphp
                                            <div wire:key="emp-sug-{{ $emp->id }}"
                                                data-employee-id="{{ $emp->id }}"
                                                @click="$wire.seleccionarEmpleado({{ $emp->id }}); open = false;"
                                                @mouseenter="activeIndex = {{ $index }}"
                                                :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-900 dark:text-blue-100': activeIndex === {{ $index }} }"
                                                class="flex items-center justify-between p-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                                
                                                <div class="min-w-0">
                                                    <div class="flex items-center gap-2 flex-wrap">
                                                        <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                                            {{ $emp->nombre_completo }}
                                                        </span>
                                                        <span class="text-xs font-mono text-gray-400 dark:text-gray-500">#{{ $emp->id }}</span>
                                                        <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded {{ $emp->tipo_empleado === 'PLANTA' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' }}">
                                                            {{ $emp->tipo_empleado }}
                                                        </span>
                                                        @if (!$emp->activo)
                                                            <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300">
                                                                Inactivo
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                        {{ $emp->puesto?->nombre ?? 'Sin puesto' }} · {{ $emp->area?->nombre ?? 'Sin área' }}
                                                    </p>
                                                </div>

                                                <div class="flex items-center gap-2 flex-shrink-0 ml-3">
                                                    @if (isset($empleadosEnPeriodoInfo[$emp->id]))
                                                        @php $infoEmp = $empleadosEnPeriodoInfo[$emp->id]; @endphp
                                                        @if ($infoEmp['cant_recibos'] > 0)
                                                            @if ($infoEmp['faltante'] > 0.01)
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300">
                                                                    Faltan ${{ number_format($infoEmp['faltante'], 2) }}
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300">
                                                                    100% Cubierto
                                                                </span>
                                                            @endif
                                                        @endif
                                                    @endif
                                                    <span class="text-xs font-mono font-bold text-gray-700 dark:text-gray-300">
                                                        ${{ number_format($emp->salario_base, 2) }}
                                                    </span>
                                                </div>
                                            </div>
                                        @empty
                                            <div wire:key="emp-sug-empty" class="p-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                                No se encontraron empleados activos que coincidan con "{{ $empleadoSearch }}".
                                            </div>
                                        @endforelse
                                    </div>
                                @endif
                            </div>
                        @endif
                        @error('form.id_empleado') <span class="text-xs text-red-500 font-medium block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tipo de Recibo y Fechas -->
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Tipo de Recibo *</label>
                            <select wire:model.live="form.tipo_recibo"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="NOMINA">NOMINA</option>
                                <option value="AGUINALDO">AGUINALDO</option>
                                <option value="FINIQUITO">FINIQUITO</option>
                                <option value="EXTRAORDINARIO">EXTRAORDINARIO</option>
                            </select>
                        </div>
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

                    <!-- Percepciones -->
                    <div class="p-4 bg-blue-50/50 dark:bg-gray-750 rounded-xl border border-blue-100 dark:border-gray-700 space-y-3">
                        <span class="block text-xs font-bold text-blue-900 dark:text-blue-300 uppercase">Percepciones Económicas</span>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Nómina Fiscal</label>
                                <input type="number" step="0.01" wire:model.live.debounce.350ms="form.nomina_fiscal"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg p-2 w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Dif. Efectivo</label>
                                <input type="number" step="0.01" wire:model.live.debounce.350ms="form.diferencia_efectivo"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg p-2 w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Horas Extras</label>
                                <input type="number" step="0.01" wire:model.live.debounce.350ms="form.horas_extras"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg p-2 w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Bonos / Otros</label>
                                <input type="number" step="0.01" wire:model.live.debounce.350ms="form.bonos_otros"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg p-2 w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Deducciones -->
                    <div class="p-4 bg-red-50/50 dark:bg-gray-750 rounded-xl border border-red-100 dark:border-gray-700 space-y-3">
                        <span class="block text-xs font-bold text-red-900 dark:text-red-300 uppercase">Deducciones / Descuentos</span>

                        @if ($this->deudaDisponible > 0)
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 p-2.5 bg-amber-50 border border-amber-200 rounded-lg dark:bg-amber-950/40 dark:border-amber-800">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <span class="text-xs text-amber-900 dark:text-amber-200">
                                        Empleado tiene anticipos de efectivo disponibles para amortizar por <strong>${{ number_format($this->deudaDisponible, 2) }}</strong>.
                                    </span>
                                </div>
                                <button type="button" wire:click="aplicarDescuentoAdelantoSugerido"
                                    class="text-xs px-2.5 py-1 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded shadow-sm transition whitespace-nowrap self-start sm:self-auto">
                                    Cargar descuento de adelanto
                                </button>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Amortización Adelanto Efectivo</label>
                                <input type="number" step="0.01" wire:model.live.debounce.350ms="form.descuento_adelanto"
                                    class="border border-gray-300 text-gray-900 text-sm rounded-lg p-2 w-full dark:border-gray-600 dark:text-white {{ $this->deudaDisponible <= 0 ? 'bg-gray-100 dark:bg-gray-800 text-gray-400 cursor-not-allowed' : 'bg-white dark:bg-gray-700' }}"
                                    placeholder="0.00"
                                    {{ $this->deudaDisponible <= 0 ? 'disabled' : '' }}>
                                @if ($this->deudaDisponible <= 0)
                                    <span class="text-[10px] text-gray-400 block mt-0.5">Sin saldo deudor por amortizar ($0.00)</span>
                                @else
                                    <span class="text-[10px] text-amber-600 dark:text-amber-400 font-semibold block mt-0.5">
                                        Máximo amortizable: ${{ number_format($this->deudaDisponible, 2) }}
                                    </span>
                                @endif
                                @error('form.descuento_adelanto')
                                    <span class="text-xs text-red-500 font-semibold block mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Infonavit</label>
                                <input type="number" step="0.01" wire:model.live.debounce.350ms="form.descuento_infonavit"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg p-2 w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Otros Descuentos</label>
                                <input type="number" step="0.01" wire:model.live.debounce.350ms="form.otros_descuentos"
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

                    <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        @if ($enSecuencia)
                            <button type="button" wire:click="cancelarSecuencia"
                                class="px-4 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-300 rounded-lg hover:bg-red-100 dark:bg-red-950/40 dark:text-red-300 dark:border-red-800 transition">
                                Cancelar Secuencia
                            </button>
                            
                            <div class="flex flex-wrap items-center gap-2">
                                <button type="button" wire:click="omitirSiguienteEnSecuencia"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 transition">
                                    Omitir este Empleadoz
                                </button>

                                <button type="submit"
                                    class="px-5 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition flex items-center gap-1.5">
                                    @if ($secuenciaIndice >= $secuenciaTotal)
                                        <span>Guardar y Finalizar Secuencia ✓</span>
                                    @else
                                        <span>Guardar y Siguiente ({{ $secuenciaIndice }}/{{ $secuenciaTotal }}) ➔</span>
                                    @endif
                                </button>
                            </div>
                        @else
                            <button type="button" wire:click="$set('showReciboModal', false)"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 transition">
                                Cancelar
                            </button>
                            
                            <div class="flex flex-wrap items-center gap-2">
                                @if (!$isEditingRecibo)
                                    <button type="button" wire:click="saveRecibo(true)"
                                        class="px-4 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm transition flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        <span>Guardar y Crear Siguiente</span>
                                    </button>
                                @endif

                                <button type="submit"
                                    class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition">
                                    {{ $isEditingRecibo ? 'Guardar Cambios' : 'Emitir y Cerrar' }}
                                </button>
                            </div>
                        @endif
                    </div>
                </form>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL AUDITORÍA Y SALDOS PENDIENTES -->
    @if ($showAuditoriaModal)
        <div class="fixed inset-0 z-50 overflow-y-auto m-0" role="dialog" aria-modal="true">
            <!-- Backdrop fijo (cubre el 100% de la pantalla) -->
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity m-0"
                 wire:click="$set('showAuditoriaModal', false)"
                 aria-hidden="true"></div>

            <!-- Contenedor centrado para scroll -->
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 m-0"
                 wire:click.self="$set('showAuditoriaModal', false)">
                <div class="relative w-full max-w-4xl my-6 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 text-left overflow-hidden transform transition-all flex flex-col max-h-[90vh]"
                     @click.stop>
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/80">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                Auditoría y Saldos Pendientes de Nómina
                            </h3>
                            <span class="px-2 py-0.5 text-xs font-bold rounded bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300">
                                {{ $selectedPeriodo?->tipo_periodo }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Período: <strong>{{ $selectedPeriodo?->nombre }}</strong> ({{ $selectedPeriodo?->fecha_inicio?->format('d/m/Y') }} al {{ $selectedPeriodo?->fecha_fin?->format('d/m/Y') }})
                        </p>
                    </div>
                    <button wire:click="$set('showAuditoriaModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-2xl font-bold p-1 leading-none">
                        &times;
                    </button>
                </div>

                <!-- Resumen de Métricas de la Auditoría -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 py-2.5 px-6 bg-gradient-to-r from-gray-50 to-blue-50/30 dark:from-gray-750 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="bg-white dark:bg-gray-700 py-2 px-3 rounded-xl border border-gray-200 dark:border-gray-600 shadow-sm">
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Presupuesto Estimado</span>
                        <div class="text-sm font-black text-gray-900 dark:text-white mt-0.5">
                            ${{ number_format($totalPresupuestoPeriodo, 2) }}
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-700 py-2 px-3 rounded-xl border border-gray-200 dark:border-gray-600 shadow-sm">
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Total Dispersado</span>
                        <div class="text-sm font-black text-emerald-600 dark:text-emerald-400 mt-0.5">
                            ${{ number_format($totalEmitidoPeriodo, 2) }}
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-700 py-2 px-3 rounded-xl border border-amber-300 dark:border-amber-800/80 shadow-sm bg-amber-50/40 dark:bg-amber-950/20">
                        <span class="text-[10px] font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider">Faltante por Pagar</span>
                        <div class="text-sm font-black text-amber-600 dark:text-amber-300 mt-0.5">
                            ${{ number_format($totalPendientePeriodo, 2) }}
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-700 py-2 px-3 rounded-xl border border-gray-200 dark:border-gray-600 shadow-sm">
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Empleados Incompletos</span>
                        <div class="text-sm font-black text-blue-600 dark:text-blue-400 mt-0.5">
                            {{ $conteoIncompletosPeriodo }} empleados
                        </div>
                    </div>
                </div>

                <!-- Filtros y Búsqueda dentro del Modal -->
                <div class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 bg-gray-50/50 dark:bg-gray-800">
                    <div class="flex flex-wrap items-center gap-2">
                        <!-- Pestañas de Estado de Pago -->
                        <div class="flex items-center gap-1 bg-gray-200/70 dark:bg-gray-700 p-1 rounded-lg text-xs font-semibold">
                            <button type="button" wire:click="$set('filtroAuditoria', 'TODOS')"
                                class="px-3 py-1.5 rounded-md transition {{ $filtroAuditoria === 'TODOS' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900' }}">
                                Todos con saldo
                            </button>
                            <button type="button" wire:click="$set('filtroAuditoria', 'PARCIAL')"
                                class="px-3 py-1.5 rounded-md transition {{ $filtroAuditoria === 'PARCIAL' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900' }}">
                                Pago parcial
                            </button>
                            <button type="button" wire:click="$set('filtroAuditoria', 'SIN_RECIBO')"
                                class="px-3 py-1.5 rounded-md transition {{ $filtroAuditoria === 'SIN_RECIBO' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900' }}">
                                Sin recibo
                            </button>
                            <button type="button" wire:click="$set('filtroAuditoria', 'COMPLETO')"
                                class="px-3 py-1.5 rounded-md transition {{ $filtroAuditoria === 'COMPLETO' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900' }}">
                                100% Cubiertos
                            </button>
                        </div>

                        <!-- Selector Rápido para Asignar Empleados a Finiquito / Extraordinario -->
                        @if ($selectedPeriodo && in_array($selectedPeriodo->tipo_periodo, ['FINIQUITO', 'EXTRAORDINARIO']))
                            <div class="relative w-full sm:w-64" x-data="{ openAsignar: false }">
                                <div class="relative">
                                    <input type="text"
                                        wire:model.live.debounce.300ms="searchAsignarEmpleado"
                                        @focus="openAsignar = true"
                                        @click.outside="openAsignar = false"
                                        placeholder="+ Asignar trabajador..."
                                        class="w-full text-xs pl-8 pr-3 py-1.5 bg-white border border-dashed border-indigo-400 rounded-lg dark:bg-gray-700 dark:border-indigo-500 dark:text-white focus:ring-1 focus:ring-indigo-500">
                                    <svg class="w-4 h-4 text-indigo-500 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                    </svg>
                                </div>

                                @if (!empty(trim($searchAsignarEmpleado)) && count($this->empleadosDisponiblesParaAsignar) > 0)
                                    <div x-show="openAsignar" class="absolute left-0 right-0 top-full mt-1 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 z-50 overflow-hidden divide-y divide-gray-100 dark:divide-gray-700 max-h-56 overflow-y-auto">
                                        @foreach ($this->empleadosDisponiblesParaAsignar as $empDisp)
                                            <button type="button"
                                                wire:key="asignar-emp-{{ $empDisp->id }}"
                                                wire:click="asignarEmpleadoAPeriodo({{ $empDisp->id }}, {{ (float) $empDisp->salario_base }})"
                                                @click="openAsignar = false"
                                                class="w-full text-left px-3 py-2 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 flex items-center justify-between text-xs transition">
                                                <div>
                                                    <span class="font-bold text-gray-900 dark:text-white">#{{ $empDisp->id }} {{ $empDisp->nombre_completo }}</span>
                                                    <span class="text-[10px] text-gray-500 block">{{ $empDisp->puesto?->nombre }} · {{ $empDisp->area?->nombre }}</span>
                                                </div>
                                                <div class="text-right flex-shrink-0 ml-2">
                                                    <span class="text-[9px] text-gray-400 block uppercase">Sueldo Base</span>
                                                    <span class="font-mono font-bold text-indigo-600 dark:text-indigo-400">${{ number_format($empDisp->salario_base, 2) }}</span>
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Buscador -->
                    <div class="relative w-full sm:w-72">
                        <input type="text" wire:model.live.debounce.350ms="searchAuditoria"
                            placeholder="Buscar empleado, puesto o #ID..."
                            class="w-full text-xs pl-8 pr-3 py-1.5 bg-white border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-1 focus:ring-blue-500">
                        <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                <!-- Tabla de Empleados con Saldo Incompleto -->
                <div class="overflow-y-auto flex-1 divide-y divide-gray-200 dark:divide-gray-700">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-100 dark:bg-gray-750 text-[11px] font-bold uppercase text-gray-600 dark:text-gray-300 sticky top-0 z-10">
                            <tr>
                                <th class="p-3 w-10 text-center">
                                    <input type="checkbox" wire:model.live="selectAllAuditoria"
                                        class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                </th>
                                <th class="p-3">Empleado</th>
                                <th class="p-3 text-right">Monto Acordado / Objetivo</th>
                                <th class="p-3 text-right">Emitido en Período</th>
                                <th class="p-3 text-right">Saldo Faltante</th>
                                <th class="p-3 text-right">Anticipos Deudores</th>
                                @if ($selectedPeriodo && in_array($selectedPeriodo->tipo_periodo, ['FINIQUITO', 'EXTRAORDINARIO']))
                                    <th class="p-3 w-12 text-center"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-xs">
                            @forelse ($empleadosAuditoria as $item)
                                <tr wire:key="auditoria-row-{{ $item['id_empleado'] }}" class="hover:bg-blue-50/40 dark:hover:bg-gray-700/50 transition {{ in_array((string) $item['id_empleado'], $selectedEmpleadosAuditoria) ? 'bg-blue-50/60 dark:bg-blue-950/20' : '' }}">
                                    <td class="p-3 text-center">
                                        <input type="checkbox" wire:key="auditoria-check-{{ $item['id_empleado'] }}" wire:model.live="selectedEmpleadosAuditoria" value="{{ $item['id_empleado'] }}"
                                            class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                    </td>
                                    <td class="p-3">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <div class="font-bold text-gray-900 dark:text-white">
                                                #{{ $item['id_empleado'] }} {{ $item['nombre_completo'] }}
                                            </div>
                                            <span class="text-[10px] font-semibold px-1.5 py-0.2 rounded {{ ($item['tipo_empleado'] ?? '') === 'PLANTA' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' }}">
                                                {{ $item['tipo_empleado'] ?? 'PLANTA' }}
                                            </span>
                                            @if ($item['cant_recibos'] > 0)
                                                <span class="px-1.5 py-0.2 rounded text-[10px] font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300">
                                                    {{ $item['cant_recibos'] }} abono(s)
                                                </span>
                                            @else
                                                <span class="px-1.5 py-0.2 rounded text-[10px] font-semibold bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                                    Sin abonos
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">
                                            {{ $item['puesto'] }} · {{ $item['area'] }}
                                        </div>
                                    </td>
                                    <td class="p-3 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <span class="text-xs text-gray-400 font-bold">$</span>
                                            <input type="number" step="0.01" min="0"
                                                wire:key="monto-acordado-{{ $item['id_empleado'] }}"
                                                value="{{ $item['monto_acordado'] !== null ? $item['monto_acordado'] : $item['salario_base'] }}"
                                                wire:change="actualizarMontoAcordado({{ $item['id_empleado'] }}, $event.target.value)"
                                                class="w-28 text-right font-mono font-bold text-xs py-1 px-2 border rounded-lg bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white focus:ring-1 focus:ring-indigo-500 transition shadow-sm">
                                        </div>
                                        @if ($item['monto_acordado'] !== null && (float)$item['monto_acordado'] == 0)
                                            <span class="text-[10px] text-gray-500 dark:text-gray-400 font-semibold block mt-0.5">
                                                Excluido ($0.00)
                                            </span>
                                        @elseif ($item['monto_acordado'] !== null && $item['monto_acordado'] != $item['salario_base'])
                                            <span class="text-[10px] text-indigo-600 dark:text-indigo-400 font-medium block mt-0.5">
                                                Base: ${{ number_format($item['salario_base'], 2) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-right">
                                        <div class="font-mono font-bold text-gray-900 dark:text-white">
                                            ${{ number_format($item['emitido'], 2) }}
                                        </div>
                                        <div class="text-[10px] text-gray-500">
                                            {{ $item['cant_recibos'] }} emitido{{ $item['cant_recibos'] != 1 ? 's' : '' }}
                                        </div>
                                    </td>
                                    <td class="p-3 text-right">
                                        @if ($item['cubierto'])
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                100% Cubierto
                                            </span>
                                        @else
                                            <div class="font-mono font-bold text-amber-600 dark:text-amber-400 text-sm">
                                                ${{ number_format($item['faltante'], 2) }}
                                            </div>
                                            <div class="text-[10px] text-gray-400">
                                                {{ $item['porcentaje_cubierto'] }}% cubierto
                                            </div>
                                        @endif
                                    </td>
                                    <td class="p-3 text-right">
                                        @if ($item['saldo_adelantos'] > 0)
                                            <span class="inline-flex items-center gap-1 font-mono font-semibold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 px-2 py-0.5 rounded border border-rose-200 dark:border-rose-900">
                                                ${{ number_format($item['saldo_adelantos'], 2) }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">$0.00</span>
                                        @endif
                                    </td>
                                    @if ($selectedPeriodo && in_array($selectedPeriodo->tipo_periodo, ['FINIQUITO', 'EXTRAORDINARIO']))
                                        <td class="p-3 text-center">
                                            @if ($item['cant_recibos'] === 0)
                                                <button type="button"
                                                    wire:click="quitarEmpleadoDePeriodo({{ $item['id_empleado'] }})"
                                                    title="Quitar trabajador de este período"
                                                    class="text-gray-400 hover:text-rose-600 p-1 rounded hover:bg-rose-50 dark:hover:bg-rose-950/40 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr wire:key="auditoria-empty">
                                    <td colspan="{{ $selectedPeriodo && in_array($selectedPeriodo->tipo_periodo, ['FINIQUITO', 'EXTRAORDINARIO']) ? '7' : '6' }}" class="p-8 text-center text-gray-500 dark:text-gray-400">
                                        <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-300 mx-auto flex items-center justify-center mb-2">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                        <p class="font-bold text-gray-700 dark:text-gray-200">
                                            ¡Todos los empleados están 100% cubiertos!
                                        </p>
                                        <p class="text-xs mt-1">
                                            No hay salarios pendientes de pago bajo este filtro en el período.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Footer del Modal con Acción de Secuencia -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                    <div class="text-xs text-gray-600 dark:text-gray-300">
                        <strong class="text-gray-900 dark:text-white font-bold">{{ count($selectedEmpleadosAuditoria) }}</strong> empleados seleccionados
                        @if (count($selectedEmpleadosAuditoria) > 0)
                            · Total por dispersar: <strong class="font-mono font-bold text-amber-600 dark:text-amber-400">${{ number_format($this->totalSeleccionadosAuditoria, 2) }}</strong>
                        @endif
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <button type="button" wire:click="$set('showAuditoriaModal', false)"
                            class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 transition">
                            Cerrar
                        </button>

                        <button type="button" wire:click="iniciarSecuenciaComplementaria"
                            @if (count($selectedEmpleadosAuditoria) === 0) disabled @endif
                            class="px-5 py-2 text-sm font-bold text-white rounded-lg shadow-sm transition flex items-center gap-2 {{ count($selectedEmpleadosAuditoria) > 0 ? 'bg-indigo-600 hover:bg-indigo-700 cursor-pointer shadow-indigo-600/30' : 'bg-gray-400 cursor-not-allowed opacity-60' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Iniciar Secuencia de Recibos ({{ count($selectedEmpleadosAuditoria) }})</span>
                        </button>
                    </div>
                </div>

                </div>
            </div>
        </div>
    @endif

    <!-- MODAL IMPORTAR EXCEL -->
    @if ($showImportModal)
        <div class="fixed inset-0 z-50 overflow-y-auto m-0" role="dialog" aria-modal="true">
            <!-- Backdrop fijo (cubre el 100% de la pantalla) -->
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity m-0"
                 wire:click="$set('showImportModal', false)"
                 aria-hidden="true"></div>

            <!-- Contenedor centrado para scroll -->
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 m-0"
                 wire:click.self="$set('showImportModal', false)">
                <div class="relative w-full max-w-lg my-6 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 p-6 text-left overflow-hidden transform transition-all"
                     @click.stop>
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            Importación Masiva desde Excel
                        </h3>
                        <button wire:click="$set('showImportModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xl font-bold">&times;</button>
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
        </div>
    @endif

    <!-- MODAL CONFIRMAR ELIMINAR PERÍODO -->
    @if ($showDeletePeriodoModal)
        <div class="fixed inset-0 z-50 overflow-y-auto m-0" role="dialog" aria-modal="true">
            <!-- Backdrop fijo -->
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity m-0"
                 wire:click="$set('showDeletePeriodoModal', false)"
                 aria-hidden="true"></div>

            <!-- Contenedor centrado -->
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 m-0"
                 wire:click.self="$set('showDeletePeriodoModal', false)">
                <div class="relative w-full max-w-md my-6 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 text-center overflow-hidden transform transition-all"
                     @click.stop>
                    <button wire:click="$set('showDeletePeriodoModal', false)" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xl font-bold">
                        &times;
                    </button>
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
        </div>
    @endif

    <!-- MODAL CONFIRMAR ELIMINAR RECIBO -->
    @if ($showDeleteReciboModal)
        <div class="fixed inset-0 z-50 overflow-y-auto m-0" role="dialog" aria-modal="true">
            <!-- Backdrop fijo -->
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity m-0"
                 wire:click="$set('showDeleteReciboModal', false)"
                 aria-hidden="true"></div>

            <!-- Contenedor centrado -->
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 m-0"
                 wire:click.self="$set('showDeleteReciboModal', false)">
                <div class="relative w-full max-w-md my-6 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 text-center overflow-hidden transform transition-all"
                     @click.stop>
                    <button wire:click="$set('showDeleteReciboModal', false)" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xl font-bold">
                        &times;
                    </button>
                    <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 mx-auto flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">¿Eliminar Recibo?</h3>
                    <p class="text-sm text-gray-500 mb-6">¿Estás seguro de eliminar este recibo? Los totales del período se recalcularán automáticamente y se sincronizará la deuda del empleado si tenía deducción de anticipos.</p>
                    <div class="flex justify-center gap-3">
                        <button wire:click="$set('showDeleteReciboModal', false)" class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</button>
                        <button wire:click="deleteRecibo" class="px-5 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL REPORTE CONTABLE DE RECIBOS -->
    @if ($showReporteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto m-0" role="dialog" aria-modal="true">
            <!-- Backdrop fijo -->
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity m-0"
                 wire:click="$set('showReporteModal', false)"
                 aria-hidden="true"></div>

            <!-- Contenedor centrado -->
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 m-0"
                 wire:click.self="$set('showReporteModal', false)">
                <div class="relative w-full max-w-lg my-6 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 p-6 text-left overflow-visible transform transition-all"
                     @click.stop>
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-2">
                            <div class="p-2 bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                    Reporte Contable de Recibos de Nómina
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Exportación a Excel detallada para contabilidad y administración</p>
                            </div>
                        </div>
                        <button wire:click="$set('showReporteModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xl font-bold">&times;</button>
                    </div>

                    <div class="space-y-4 text-xs">
                        <!-- Año y Período -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Año Fiscal</label>
                                <select wire:model.live="filtroReporteAnio"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Todos los años</option>
                                    @foreach ($aniosDisponibles as $a)
                                        <option value="{{ $a }}">{{ $a }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Período</label>
                                <select wire:model="filtroReportePeriodoId"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Todos los períodos del año</option>
                                    @foreach ($periodosReporte as $p)
                                        <option value="{{ $p->id }}">{{ $p->nombre }} ({{ $p->fecha_inicio?->format('d/m/Y') }} - {{ $p->fecha_fin?->format('d/m/Y') }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Empleado (Combobox con búsqueda reactiva y navegación con teclado) -->
                        <div x-data="{
                            open: false,
                            activeIndex: 0,
                            getItems() {
                                return $el.querySelectorAll('[data-report-employee-id]');
                            },
                            nextItem() {
                                const items = this.getItems();
                                if (items.length === 0) return;
                                this.open = true;
                                this.activeIndex = (this.activeIndex + 1) % items.length;
                                this.scrollToActive();
                            },
                            prevItem() {
                                const items = this.getItems();
                                if (items.length === 0) return;
                                this.open = true;
                                this.activeIndex = (this.activeIndex - 1 + items.length) % items.length;
                                this.scrollToActive();
                            },
                            selectCurrent() {
                                if (!this.open) return;
                                const items = this.getItems();
                                if (items.length > 0 && items[this.activeIndex]) {
                                    const empId = items[this.activeIndex].getAttribute('data-report-employee-id');
                                    const empNombre = items[this.activeIndex].getAttribute('data-report-employee-name');
                                    if (empId) {
                                        $wire.seleccionarEmpleadoReporte(parseInt(empId), empNombre);
                                        this.open = false;
                                    }
                                }
                            },
                            scrollToActive() {
                                const items = this.getItems();
                                if (items[this.activeIndex]) {
                                    items[this.activeIndex].scrollIntoView({ block: 'nearest' });
                                }
                            }
                        }"
                        x-init="
                            $watch('$wire.filtroReporteEmpleadoSearch', value => {
                                if (value && value.trim().length > 0) {
                                    open = true;
                                    activeIndex = 0;
                                }
                            });
                        "
                        @click.outside="open = false"
                        class="relative">
                            <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">
                                Empleado Específico <span class="font-normal text-gray-400 lowercase">(opcional)</span>
                            </label>
                            @if ($filtroReporteEmpleadoId)
                                <div class="flex items-center justify-between p-2.5 bg-blue-50 border border-blue-200 rounded-lg dark:bg-blue-900/30 dark:border-blue-800">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span class="font-bold text-blue-900 dark:text-blue-200">{{ $filtroReporteEmpleadoNombre }}</span>
                                    </div>
                                    <button wire:click="limpiarEmpleadoReporte" type="button"
                                        class="text-xs text-rose-600 hover:text-rose-800 font-bold px-2 py-0.5 rounded hover:bg-rose-50 transition"
                                        title="Quitar empleado (incluir todos)">
                                        &times; Quitar
                                    </button>
                                </div>
                            @else
                                <div class="relative">
                                    <input type="text" 
                                        x-ref="searchInputReporte"
                                        wire:model.live.debounce.350ms="filtroReporteEmpleadoSearch"
                                        @focus="if ($wire.filtroReporteEmpleadoSearch && $wire.filtroReporteEmpleadoSearch.trim().length > 0) open = true"
                                        @input="open = true; activeIndex = 0"
                                        @keydown.down.prevent="nextItem()"
                                        @keydown.up.prevent="prevItem()"
                                        @keydown.enter.prevent.stop="selectCurrent()"
                                        @keydown.escape.prevent.stop="open = false"
                                        autocomplete="off"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 ps-8 pr-8 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="Escribe ID o nombre para buscar... (Vacío = Todos los empleados)">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-2.5 pointer-events-none">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                    @if ($filtroReporteEmpleadoSearch)
                                        <button type="button" 
                                            wire:click="$set('filtroReporteEmpleadoSearch', '')" 
                                            @click="open = false"
                                            class="absolute inset-y-0 end-0 pe-2.5 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                            &times;
                                        </button>
                                    @endif
                                </div>

                                @if (strlen(trim($filtroReporteEmpleadoSearch)) > 0)
                                    <div x-show="open" 
                                        x-cloak 
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="transform opacity-0 scale-95"
                                        x-transition:enter-end="transform opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="transform opacity-100 scale-100"
                                        x-transition:leave-end="transform opacity-0 scale-95"
                                        class="absolute z-50 left-0 right-0 mt-1 max-h-52 overflow-y-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-xl divide-y divide-gray-100 dark:divide-gray-700">
                                        @forelse ($empleadosReporteBusqueda as $index => $e)
                                            <div wire:key="rep-emp-{{ $e->id }}"
                                                data-report-employee-id="{{ $e->id }}"
                                                data-report-employee-name="{{ addslashes($e->nombre_completo) }}"
                                                @click="$wire.seleccionarEmpleadoReporte({{ $e->id }}, '{{ addslashes($e->nombre_completo) }}'); open = false;"
                                                @mouseenter="activeIndex = {{ $index }}"
                                                :class="{ 'bg-blue-50 dark:bg-blue-900/40 text-blue-900 dark:text-blue-100': activeIndex === {{ $index }} }"
                                                class="w-full text-left px-3 py-2 text-xs cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 flex items-center justify-between transition">
                                                <div>
                                                    <span class="font-bold text-gray-900 dark:text-white">#{{ $e->id }} - {{ $e->nombre_completo }}</span>
                                                    <span class="text-[11px] text-gray-400 block">{{ $e->puesto?->nombre }} · {{ $e->area?->nombre }}</span>
                                                </div>
                                                <span class="px-1.5 py-0.5 text-[10px] rounded font-semibold {{ $e->activo ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-750 dark:text-gray-400' }}">
                                                    {{ $e->activo ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </div>
                                        @empty
                                            <div wire:key="rep-emp-empty" class="px-3 py-3 text-center text-xs text-gray-400 dark:text-gray-500">
                                                No se encontraron empleados coincidentes con "<span class="font-semibold text-gray-600 dark:text-gray-300">{{ $filtroReporteEmpleadoSearch }}</span>"
                                            </div>
                                        @endforelse
                                    </div>
                                @endif
                            @endif
                        </div>

                        <!-- Método de Pago y Estado de Pago -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Método de Pago</label>
                                <select wire:model="filtroReporteMetodoPago"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="TODOS">Todos los métodos</option>
                                    <option value="EFECTIVO">Efectivo</option>
                                    <option value="TRANSFERENCIA">Transferencia</option>
                                    <option value="CHEQUE">Cheque</option>
                                    <option value="OTRO">Otro</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-1">Estado de Pago</label>
                                <select wire:model="filtroReporteEstadoPago"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="TODOS">Todos los estados</option>
                                    <option value="PAGADO">Pagado</option>
                                    <option value="PENDIENTE">Pendiente</option>
                                </select>
                            </div>
                        </div>

                        <!-- Información Contable que incluye -->
                        <div class="p-3 bg-gray-50 dark:bg-gray-750/70 rounded-xl border border-gray-200 dark:border-gray-700">
                            <span class="text-[11px] font-bold text-gray-700 dark:text-gray-300 uppercase block mb-1">Columnas incluidas en el reporte:</span>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-relaxed">
                                Folio, Período, ID, Trabajador, Área, Puesto, Tipo Empleado, Tipo Recibo, Método, Estado, Fecha Pago,
                                <strong>Sueldo Fiscal</strong>, <strong>Dif. Efectivo</strong>, <strong>Horas Extras</strong>, <strong>Bonos</strong>, <strong>Total Percepciones</strong>,
                                <strong>Amort. Anticipos</strong>, <strong>Infonavit</strong>, <strong>Otros Desc.</strong>, <strong>Total Deducciones</strong>,
                                <strong>Neto a Pagar</strong> y fila de <strong>Sumatorias Totales</strong>.
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700 mt-5">
                        <button type="button" wire:click="$set('showReporteModal', false)"
                            class="px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                            Cerrar
                        </button>

                        <a href="{{ route('nomina.excel.reporte-recibos', [
                                'anio' => $filtroReporteAnio,
                                'id_periodo' => $filtroReportePeriodoId,
                                'id_empleado' => $filtroReporteEmpleadoId,
                                'metodo_pago' => $filtroReporteMetodoPago,
                                'estado_pago' => $filtroReporteEstadoPago,
                            ]) }}" target="_blank"
                            @click="$wire.set('showReporteModal', false)"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 shadow-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Descargar Reporte Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('focus-empleado-search', () => {
                setTimeout(() => {
                    const el = document.getElementById('input-empleado-search');
                    if (el) {
                        el.focus();
                        el.select();
                    }
                }, 100);
            });
        });
    </script>
</div>