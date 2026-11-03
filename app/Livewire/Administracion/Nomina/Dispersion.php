<?php

namespace App\Livewire\Administracion\Nomina;

use App\Exports\NominaPlantillaExport;
use App\Imports\NominaPlantillaImport;
use App\Livewire\Forms\NominaReciboForm;
use App\Models\NominaEmpleado;
use App\Models\NominaMovimientoCuenta;
use App\Models\NominaPeriodo;
use App\Models\NominaPeriodoEmpleado;
use App\Models\NominaRecibo;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Dispersion extends Component
{
    use WithPagination, WithFileUploads;

    public NominaReciboForm $form;

    // Período Activo y Filtros de Cabecera
    public ?int $selectedPeriodoId = null;
    public string $periodoSearch = '';
    public int|string $filtroAnioPeriodo = '';
    public string $filtroTipoPeriodo = 'TODOS';
    public bool $showPeriodoModal = false;
    public bool $isEditingPeriodo = false;

    // Formulario Período
    public ?int $periodoId = null;
    public string $periodoNombre = '';
    public string $periodoTipo = 'QUINCENAL'; // DIARIO, SEMANAL, QUINCENAL, MENSUAL, AGUINALDO, FINIQUITO, EXTRAORDINARIO
    public string $periodoFechaInicio = '';
    public string $periodoFechaFin = '';
    public string $periodoFechaPago = '';
    public bool $periodoFechaPagoManual = false;
    public string $periodoEstado = 'BORRADOR'; // BORRADOR, EN_PROCESO, DISPERSADO, CERRADO

    // Filtros de Recibos
    public string $reciboSearch = '';
    public string $filtroTipoRecibo = 'TODOS';
    public string $filtroEstadoPago = 'TODOS';
    public string $filtroMetodoPago = 'TODOS';

    // Modal Recibo
    public bool $showReciboModal = false;
    public bool $isEditingRecibo = false;
    public bool $darDeBajaAlFiniquitar = false;
    public string $empleadoSearch = '';
    public ?NominaEmpleado $empleadoSeleccionado = null;

    // Selección múltiple para impresión
    public array $selectedRecibos = [];
    public bool $selectAll = false;

    // Importación Excel
    public bool $showImportModal = false;
    public $archivoExcel = null;
    public array $importErrors = [];

    // Confirmaciones
    public bool $showDeletePeriodoModal = false;
    public bool $showDeleteReciboModal = false;
    public ?int $reciboIdToDelete = null;

    // Mensajes
    public string $successMessage = '';
    public string $errorMessage = '';

    // Acordeón de detalles expandidos
    public array $expandedRecibos = [];

    // Modal Auditoría y Saldos Pendientes
    public bool $showAuditoriaModal = false;
    public array $selectedEmpleadosAuditoria = [];
    public bool $selectAllAuditoria = false;
    public string $filtroAuditoria = 'TODOS'; // TODOS, PARCIAL, SIN_RECIBO, COMPLETO
    public string $searchAuditoria = '';
    public string $searchAsignarEmpleado = '';
    public float|string $montoAsignarEmpleado = '';

    // Aviso empleado fuera del período de labores
    public bool $empleadoFueraDePeriodo = false;
    public ?string $motivoEmpleadoFueraDePeriodo = null;

    // Secuencia de Recibos Complementarios
    public bool $enSecuencia = false;
    public array $colaSecuencia = [];
    public int $secuenciaTotal = 0;
    public int $secuenciaIndice = 0;
    public float $saldoPendienteEmpleadoActual = 0.0;
    public float $salarioBaseEmpleadoActual = 0.0;
    public float $totalEmitidoPrevioEmpleadoActual = 0.0;
    public int $recibosPreviosCountEmpleadoActual = 0;

    // Modal Reporte Contable de Recibos
    public bool $showReporteModal = false;
    public ?int $filtroReporteAnio = null;
    public ?int $filtroReportePeriodoId = null;
    public ?int $filtroReporteEmpleadoId = null;
    public ?string $filtroReporteEmpleadoNombre = null;
    public string $filtroReporteEmpleadoSearch = '';
    public string $filtroReporteMetodoPago = 'TODOS';
    public string $filtroReporteEstadoPago = 'TODOS';

    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        // Seleccionar por defecto el periodo más reciente y su año
        $ultimoPeriodo = NominaPeriodo::orderBy('fecha_inicio', 'desc')->first();
        if ($ultimoPeriodo) {
            $this->selectedPeriodoId = $ultimoPeriodo->id;
            $this->filtroAnioPeriodo = $ultimoPeriodo->fecha_inicio ? (int) $ultimoPeriodo->fecha_inicio->year : (int) now()->year;
        } else {
            $this->filtroAnioPeriodo = (int) now()->year;
        }
        $this->filtroTipoPeriodo = 'TODOS';

        $this->periodoFechaInicio = now()->startOfMonth()->toDateString();
        $this->periodoFechaFin = now()->endOfMonth()->toDateString();
        $this->periodoFechaPago = now()->toDateString();
    }

    public function updatingReciboSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroTipoRecibo(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroEstadoPago(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroMetodoPago(): void
    {
        $this->resetPage();
    }

    public function updatedFiltroAnioPeriodo(): void
    {
        $this->selectedPeriodoId = null;
        $this->selectedRecibos = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    public function updatedFiltroTipoPeriodo(): void
    {
        $this->selectedPeriodoId = null;
        $this->selectedRecibos = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    public function updatedSelectedPeriodoId($id): void
    {
        $this->selectedPeriodoId = !empty($id) ? (int) $id : null;
        $this->selectedRecibos = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    public function selectPeriodo($id): void
    {
        $this->updatedSelectedPeriodoId($id);
    }

    public function updatedSelectAll($value): void
    {
        if ($value && $this->selectedPeriodoId) {
            $this->selectedRecibos = NominaRecibo::where('id_periodo', $this->selectedPeriodoId)
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedRecibos = [];
        }
    }

    public function toggleExpand(int $reciboId): void
    {
        if (in_array($reciboId, $this->expandedRecibos)) {
            $this->expandedRecibos = array_diff($this->expandedRecibos, [$reciboId]);
        } else {
            $this->expandedRecibos[] = $reciboId;
        }
    }

    // --- GESTIÓN DE PERÍODOS ---

    public function createPeriodo(): void
    {
        $this->periodoId = null;
        $this->periodoNombre = 'Nómina ' . date('d/m/Y');
        $this->periodoTipo = 'QUINCENAL';
        $this->periodoFechaInicio = now()->toDateString();
        $this->periodoFechaFin = now()->addDays(14)->toDateString();
        $this->periodoFechaPago = now()->addDays(14)->toDateString();
        $this->periodoFechaPagoManual = false;
        $this->periodoEstado = 'BORRADOR';
        $this->isEditingPeriodo = false;
        $this->showPeriodoModal = true;
    }

    public function editPeriodo(int $id): void
    {
        $p = NominaPeriodo::findOrFail($id);
        $this->periodoId = $p->id;
        $this->periodoNombre = $p->nombre;
        $this->periodoTipo = $p->tipo_periodo;
        $this->periodoFechaInicio = $p->fecha_inicio ? $p->fecha_inicio->format('Y-m-d') : '';
        $this->periodoFechaFin = $p->fecha_fin ? $p->fecha_fin->format('Y-m-d') : '';
        $this->periodoFechaPago = $p->fecha_pago ? $p->fecha_pago->format('Y-m-d') : '';
        $this->periodoFechaPagoManual = (!empty($this->periodoFechaPago) && $this->periodoFechaPago !== $this->periodoFechaFin);
        $this->periodoEstado = $p->estado;
        $this->isEditingPeriodo = true;
        $this->showPeriodoModal = true;
    }

    public function updatedPeriodoTipo($tipo): void
    {
        // Si el tipo es DIARIO (meseros, cocineros eventuales de 1 solo día)
        if ($tipo === 'DIARIO') {
            $this->periodoFechaFin = $this->periodoFechaInicio;
            $this->periodoFechaPago = $this->periodoFechaInicio;
            $this->periodoFechaPagoManual = false;
        }
    }

    public function updatedPeriodoFechaInicio($val): void
    {
        if ($this->periodoTipo === 'DIARIO') {
            $this->periodoFechaFin = $val;
            $this->periodoFechaPago = $val;
            $this->periodoFechaPagoManual = false;
        }
    }

    public function updatedPeriodoFechaFin($val): void
    {
        if (!$this->periodoFechaPagoManual || empty($this->periodoFechaPago)) {
            $this->periodoFechaPago = $val;
        }
    }

    public function updatedPeriodoFechaPago($val): void
    {
        $this->periodoFechaPagoManual = (!empty($val) && $val !== $this->periodoFechaFin);
    }

    public function savePeriodo(): void
    {
        $this->validate([
            'periodoNombre' => 'required|string|max:100',
            'periodoTipo' => 'required|in:DIARIO,SEMANAL,QUINCENAL,MENSUAL,AGUINALDO,FINIQUITO,EXTRAORDINARIO',
            'periodoFechaInicio' => 'required|date',
            'periodoFechaFin' => 'required|date|after_or_equal:periodoFechaInicio',
            'periodoFechaPago' => 'nullable|date',
            'periodoEstado' => 'required|in:BORRADOR,EN_PROCESO,APROBADO,DISPERSADO,PAGADO,CERRADO',
        ]);

        if ($this->isEditingPeriodo && $this->periodoId) {
            $p = NominaPeriodo::findOrFail($this->periodoId);
            $p->update([
                'nombre' => $this->periodoNombre,
                'tipo_periodo' => $this->periodoTipo,
                'fecha_inicio' => $this->periodoFechaInicio,
                'fecha_fin' => $this->periodoFechaFin,
                'fecha_pago' => $this->periodoFechaPago ?: null,
                'estado' => $this->periodoEstado,
            ]);
            $p->recalcularTotales();
            $this->filtroAnioPeriodo = (int) date('Y', strtotime($this->periodoFechaInicio));
            $this->selectedPeriodoId = $p->id;
            $this->dispatch('toast', message: "Período '{$p->nombre}' actualizado con éxito.", type: 'success');
        } else {
            $folio = 'PER-' . date('Y') . '-' . strtoupper(substr(uniqid(), -5));
            $p = NominaPeriodo::create([
                'folio' => $folio,
                'nombre' => $this->periodoNombre,
                'tipo_periodo' => $this->periodoTipo,
                'fecha_inicio' => $this->periodoFechaInicio,
                'fecha_fin' => $this->periodoFechaFin,
                'fecha_pago' => $this->periodoFechaPago ?: now()->toDateString(),
                'estado' => $this->periodoEstado,
                'id_usuario' => auth()->id(),
            ]);
            $this->filtroAnioPeriodo = (int) date('Y', strtotime($this->periodoFechaInicio));
            $this->filtroTipoPeriodo = 'TODOS';
            $this->selectedPeriodoId = $p->id;
            $this->dispatch('toast', message: "Período '{$p->nombre}' creado exitosamente.", type: 'success');
        }

        $this->showPeriodoModal = false;
    }

    public function confirmDeletePeriodo(): void
    {
        $this->showDeletePeriodoModal = true;
    }

    public function deletePeriodo(): void
    {
        if ($this->selectedPeriodoId) {
            $p = NominaPeriodo::find($this->selectedPeriodoId);
            if ($p) {
                if ($p->estado === 'CERRADO') {
                    $this->dispatch('toast', message: 'No se puede eliminar un período con estado CERRADO. Actualiza su estado primero si deseas gestionarlo.', type: 'error');
                    $this->showDeletePeriodoModal = false;
                    return;
                }

                DB::transaction(function () use ($p) {
                    $empleadosIds = $p->recibos()->pluck('id_empleado')->unique()->filter();
                    NominaMovimientoCuenta::where('id_periodo', $p->id)->where('tipo_movimiento', 'DESCUENTO_NOMINA')->delete();
                    $p->recibos()->delete();
                    $p->delete();

                    foreach ($empleadosIds as $empId) {
                        NominaEmpleado::find($empId)?->reconciliarKardexSaldos();
                    }
                });

                $this->dispatch('toast', message: 'Período y sus recibos eliminados.', type: 'info');
            }
            $this->selectedPeriodoId = null;
        }
        $this->showDeletePeriodoModal = false;
    }

    public function recalcularTotalesPeriodo(): void
    {
        if ($this->selectedPeriodoId) {
            $p = NominaPeriodo::find($this->selectedPeriodoId);
            $p?->recalcularTotales();
            $this->dispatch('toast', message: 'Totales del período recalculados.', type: 'success');
        }
    }

    // --- GESTIÓN DE RECIBOS ---

    public function createRecibo(): void
    {
        if (!$this->selectedPeriodoId) {
            $this->dispatch('toast', message: 'Por favor selecciona un período de nómina primero.', type: 'warning');
            return;
        }

        $p = NominaPeriodo::find($this->selectedPeriodoId);
        if ($p && $p->estado === 'CERRADO') {
            $this->dispatch('toast', message: 'No se pueden crear nuevos recibos en un período CERRADO.', type: 'warning');
            return;
        }

        $this->form->reset();
        $this->limpiarEmpleadoSeleccionado();
        $this->form->id_periodo = $this->selectedPeriodoId;
        $this->form->metodo_pago = 'EFECTIVO';
        $this->form->estado_pago = 'PENDIENTE';
        $this->form->tipo_recibo = 'NOMINA';
        $this->empleadoFueraDePeriodo = false;
        $this->motivoEmpleadoFueraDePeriodo = null;

        if ($p) {
            if ($p) {
                $this->form->fecha_inicio = $p->fecha_inicio ? $p->fecha_inicio->format('Y-m-d') : '';
                $this->form->fecha_fin = $p->fecha_fin ? $p->fecha_fin->format('Y-m-d') : '';
                $this->form->fecha_pago = $p->fecha_pago ? $p->fecha_pago->format('Y-m-d') : now()->toDateString();
                if ($p->tipo_periodo === 'AGUINALDO') {
                    $this->form->tipo_recibo = 'AGUINALDO';
                } elseif ($p->tipo_periodo === 'FINIQUITO') {
                    $this->form->tipo_recibo = 'FINIQUITO';
                }
            }
        }

        $this->isEditingRecibo = false;
        $this->darDeBajaAlFiniquitar = false;
        $this->showReciboModal = true;
        $this->dispatch('focus-empleado-search');
    }

    public function seleccionarEmpleado(int $id): void
    {
        $empleado = NominaEmpleado::with(['area', 'puesto', 'historialLaboral'])->find($id);
        if (!$empleado) {
            return;
        }

        $this->form->id_empleado = $empleado->id;
        $this->empleadoSeleccionado = $empleado;
        $this->empleadoSearch = '';

        // Validar si el trabajador tiene ciclo laboral en el rango de este período
        $periodo = $this->selectedPeriodoId ? NominaPeriodo::find($this->selectedPeriodoId) : null;
        if ($periodo) {
            $laboroEnPeriodo = $empleado->historialLaboral()
                ->laborandoEnRango($periodo->fecha_inicio, $periodo->fecha_fin)
                ->exists();

            if (!$laboroEnPeriodo) {
                $this->empleadoFueraDePeriodo = true;
                $fIni = $periodo->fecha_inicio ? $periodo->fecha_inicio->format('d/m/Y') : '';
                $fFin = $periodo->fecha_fin ? $periodo->fecha_fin->format('d/m/Y') : '';
                $this->motivoEmpleadoFueraDePeriodo = "Aviso: {$empleado->nombre_completo} no cuenta con ciclo de labores activo dentro de las fechas de este período ({$fIni} al {$fFin}). Puedes continuar si se trata de un recibo complementario o extemporáneo.";
            } else {
                $this->empleadoFueraDePeriodo = false;
                $this->motivoEmpleadoFueraDePeriodo = null;
            }
        } else {
            $this->empleadoFueraDePeriodo = false;
            $this->motivoEmpleadoFueraDePeriodo = null;
        }

        // Verificar si ya tiene recibos en este período para precarga complementaria
        $recibosPrevios = $this->selectedPeriodoId
            ? NominaRecibo::where('id_periodo', $this->selectedPeriodoId)->where('id_empleado', $empleado->id)->get()
            : collect([]);

        // Buscar salario base del ciclo laboral activo durante el período o del empleado
        $ciclo = null;
        if ($periodo) {
            $ciclo = $empleado->historialLaboral->first(function ($c) use ($periodo) {
                return $c->fecha_ingreso <= $periodo->fecha_fin && ($c->fecha_baja === null || $c->fecha_baja >= $periodo->fecha_inicio);
            });
        }
        $salarioBasePeriodo = $ciclo ? (float) $ciclo->salario_base : (float) $empleado->salario_base;

        $asignacion = $this->selectedPeriodoId
            ? NominaPeriodoEmpleado::where('id_periodo', $this->selectedPeriodoId)->where('id_empleado', $empleado->id)->first()
            : null;
        $montoAcordado = $asignacion ? (float) $asignacion->monto_acordado : 0.0;
        $montoObjetivo = $montoAcordado > 0 ? $montoAcordado : $salarioBasePeriodo;

        $this->form->monto_total_acordado = $montoObjetivo;
        $this->recibosPreviosCountEmpleadoActual = $recibosPrevios->count();
        $this->salarioBaseEmpleadoActual = $montoObjetivo;
        $this->totalEmitidoPrevioEmpleadoActual = (float) $recibosPrevios->sum('total_percepciones');
        $this->saldoPendienteEmpleadoActual = max(0, round($montoObjetivo - $this->totalEmitidoPrevioEmpleadoActual, 2));

        if ($periodo && in_array($periodo->tipo_periodo, ['AGUINALDO', 'FINIQUITO'])) {
            $this->form->tipo_recibo = $periodo->tipo_periodo;
        }

        if ($recibosPrevios->isNotEmpty()) {
            // Empleado con recibos previos (abono complementario)
            $montoAsignar = $this->saldoPendienteEmpleadoActual;
            $ultimoRecibo = $recibosPrevios->sortByDesc('id')->first();
            if ($ultimoRecibo) {
                $this->form->tipo_recibo = $ultimoRecibo->tipo_recibo;
                $this->form->metodo_pago = $ultimoRecibo->metodo_pago;
            }
            $numComplemento = $this->recibosPreviosCountEmpleadoActual + 1;
            $this->form->observaciones = "Abono #{$numComplemento} - Saldo restante";
        } else {
            $montoAsignar = $this->saldoPendienteEmpleadoActual > 0 ? $this->saldoPendienteEmpleadoActual : $montoObjetivo;
            $this->form->metodo_pago = $empleado->tipo_pago_predeterminado ?: 'EFECTIVO';
        }

        // Precarga de montos según método de pago
        if ($this->form->metodo_pago === 'TRANSFERENCIA') {
            $this->form->nomina_fiscal = $montoAsignar;
            $this->form->diferencia_efectivo = 0;
        } else {
            $this->form->nomina_fiscal = 0;
            $this->form->diferencia_efectivo = $montoAsignar;
        }
    }

    public function limpiarEmpleadoSeleccionado(): void
    {
        $this->form->id_empleado = null;
        $this->form->monto_total_acordado = null;
        $this->empleadoSeleccionado = null;
        $this->empleadoSearch = '';
        $this->form->nomina_fiscal = 0;
        $this->form->diferencia_efectivo = 0;
        $this->saldoPendienteEmpleadoActual = 0.0;
        $this->salarioBaseEmpleadoActual = 0.0;
        $this->totalEmitidoPrevioEmpleadoActual = 0.0;
        $this->recibosPreviosCountEmpleadoActual = 0;
        $this->empleadoFueraDePeriodo = false;
        $this->motivoEmpleadoFueraDePeriodo = null;
        $this->dispatch('focus-empleado-search');
    }

    public function editRecibo(int $id): void
    {
        $recibo = NominaRecibo::with('periodo')->findOrFail($id);
        if ($recibo->periodo && $recibo->periodo->estado === 'CERRADO') {
            $this->dispatch('toast', message: 'No se puede editar un recibo de un período CERRADO.', type: 'warning');
            return;
        }

        $this->form->setRecibo($recibo);
        $this->empleadoSeleccionado = NominaEmpleado::with(['area', 'puesto'])->find($recibo->id_empleado);
        $this->empleadoSearch = '';

        $acordado = NominaPeriodoEmpleado::where('id_periodo', $recibo->id_periodo)
            ->where('id_empleado', $recibo->id_empleado)
            ->value('monto_acordado');
        $montoObjetivo = ($acordado !== null && (float) $acordado > 0) ? (float) $acordado : (float) ($this->empleadoSeleccionado?->salario_base ?? 0);

        $recibosPrevios = NominaRecibo::where('id_periodo', $recibo->id_periodo)
            ->where('id_empleado', $recibo->id_empleado)
            ->get();

        $this->recibosPreviosCountEmpleadoActual = $recibosPrevios->count();
        $this->salarioBaseEmpleadoActual = $montoObjetivo;
        $this->totalEmitidoPrevioEmpleadoActual = (float) $recibosPrevios->sum('total_percepciones');
        $this->saldoPendienteEmpleadoActual = max(0, round($montoObjetivo - $this->totalEmitidoPrevioEmpleadoActual, 2));
        $this->isEditingRecibo = true;
        $this->darDeBajaAlFiniquitar = false;
        $this->empleadoFueraDePeriodo = false;
        $this->motivoEmpleadoFueraDePeriodo = null;
        $this->showReciboModal = true;
    }

    public function saveRecibo(bool $crearOtro = false): void
    {
        if ($this->form->id_periodo) {
            $periodo = NominaPeriodo::find($this->form->id_periodo);
            if ($periodo && $periodo->estado === 'CERRADO') {
                $this->dispatch('toast', message: 'Operación bloqueada: El período de nómina está CERRADO.', type: 'error');
                return;
            }
        }

        try {
            if ($this->isEditingRecibo) {
                $this->form->update();
                $this->dispatch('toast', message: 'Recibo de nómina actualizado correctamente.', type: 'success');
                $this->showReciboModal = false;
            } else {
                $savedFechaInicio = $this->form->fecha_inicio;
                $savedFechaFin = $this->form->fecha_fin;
                $savedFechaPago = $this->form->fecha_pago;
                $savedIdPeriodo = $this->form->id_periodo;
                $savedTipoRecibo = $this->form->tipo_recibo;
                $idEmpleadoOriginal = $this->form->id_empleado;

                $recibo = $this->form->store();

                // Si fue finiquito y el usuario optó por dar de baja al empleado
                if ($savedTipoRecibo === 'FINIQUITO' && $this->darDeBajaAlFiniquitar && $idEmpleadoOriginal) {
                    $emp = NominaEmpleado::find($idEmpleadoOriginal);
                    if ($emp) {
                        $fechaBaja = $savedFechaPago ?: now()->toDateString();
                        $emp->update([
                            'activo' => false,
                            'fecha_baja' => $fechaBaja,
                        ]);
                        // Cerrar ciclo laboral activo en el historial
                        $cicloActivo = $emp->cicloLaboralActivo;
                        if ($cicloActivo) {
                            $periodoNombre = $savedIdPeriodo ? (NominaPeriodo::find($savedIdPeriodo)?->nombre ?? '') : '';
                            $cicloActivo->update([
                                'fecha_baja' => $fechaBaja,
                                'motivo_baja' => 'Baja por finiquito' . ($periodoNombre ? " (Período: {$periodoNombre})" : ''),
                            ]);
                        }
                    }
                    $this->dispatch('toast', message: 'Recibo de finiquito creado y empleado dado de baja exitosamente.', type: 'success');
                } else {
                    $this->dispatch('toast', message: "Recibo de {$recibo->nombre_empleado_snapshot} emitido exitosamente.", type: 'success');
                }

                if ($this->enSecuencia) {
                    // Pasar de inmediato al siguiente empleado en la secuencia
                    $this->cargarSiguienteEnSecuencia();
                } elseif ($crearOtro) {
                    // Mantener el modal abierto, limpiar el empleado seleccionado y enfocar el buscador
                    $this->limpiarEmpleadoSeleccionado();
                    $this->form->id_periodo = $savedIdPeriodo;
                    $this->form->tipo_recibo = $savedTipoRecibo;
                    $this->form->fecha_inicio = $savedFechaInicio;
                    $this->form->fecha_fin = $savedFechaFin;
                    $this->form->fecha_pago = $savedFechaPago;
                    $this->form->horas_extras = 0;
                    $this->form->bonos_otros = 0;
                    $this->form->monto_aguinaldo = 0;
                    $this->form->monto_vacaciones = 0;
                    $this->form->monto_prima_vacacional = 0;
                    $this->form->descuento_adelanto = 0;
                    $this->form->descuento_infonavit = 0;
                    $this->form->otros_descuentos = 0;
                    $this->form->observaciones = null;
                    $this->darDeBajaAlFiniquitar = false;
                    $this->dispatch('focus-empleado-search');
                } else {
                    $this->showReciboModal = false;
                }
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Error al procesar recibo: ' . $e->getMessage(), type: 'error');
        }
    }

    // --- AUDITORÍA Y SALDOS PENDIENTES ---

    public function openAuditoriaModal(): void
    {
        if (!$this->selectedPeriodoId) {
            $this->dispatch('toast', message: 'Por favor selecciona un período de nómina primero.', type: 'warning');
            return;
        }

        $this->selectedEmpleadosAuditoria = [];
        $this->selectAllAuditoria = false;
        $this->filtroAuditoria = 'TODOS';
        $this->searchAuditoria = '';
        $this->searchAsignarEmpleado = '';
        $this->montoAsignarEmpleado = '';
        $this->showAuditoriaModal = true;
    }

    public function updatedSelectAllAuditoria($value): void
    {
        if ($value && $this->selectedPeriodoId) {
            $empleados = $this->obtenerEmpleadosAuditoria();
            $this->selectedEmpleadosAuditoria = array_map(fn($item) => (string) $item['id_empleado'], $empleados);
        } else {
            $this->selectedEmpleadosAuditoria = [];
        }
    }

    public function obtenerEmpleadosAuditoria(): array
    {
        if (!$this->selectedPeriodoId) {
            return [];
        }

        $periodo = NominaPeriodo::find($this->selectedPeriodoId);
        if (!$periodo) {
            return [];
        }

        $esFiniquitoOExtraordinario = in_array(strtoupper($periodo->tipo_periodo ?? ''), ['FINIQUITO', 'EXTRAORDINARIO']);

        $query = NominaEmpleado::with([
            'area',
            'puesto',
            'historialLaboral',
            'asignacionesPeriodos' => fn($q) => $q->where('id_periodo', $periodo->id),
        ]);

        if ($esFiniquitoOExtraordinario) {
            $query->where(function ($q) use ($periodo) {
                $q->whereHas('asignacionesPeriodos', fn($aq) => $aq->where('id_periodo', $periodo->id))
                  ->orWhereHas('recibos', fn($rq) => $rq->where('id_periodo', $periodo->id));
            });
        } else {
            $query->where(function ($q) use ($periodo) {
                $q->laborandoEnPeriodo($periodo->fecha_inicio, $periodo->fecha_fin)
                  ->orWhereHas('recibos', fn($rq) => $rq->where('id_periodo', $periodo->id));
            });
        }

        $empleados = $query->orderBy('nombre', 'asc')->get();
        $recibos = NominaRecibo::where('id_periodo', $this->selectedPeriodoId)->get()->groupBy('id_empleado');
        $resultado = [];
        $search = mb_strtolower(trim($this->searchAuditoria));

        foreach ($empleados as $emp) {
            // Determinar salario base correspondiente al período
            $ciclo = $emp->historialLaboral->first(function ($c) use ($periodo) {
                return $c->fecha_ingreso <= $periodo->fecha_fin && ($c->fecha_baja === null || $c->fecha_baja >= $periodo->fecha_inicio);
            });
            $salarioBase = $ciclo ? (float) $ciclo->salario_base : (float) $emp->salario_base;

            $asignacion = $emp->asignacionesPeriodos->first();
            $montoAcordado = $asignacion ? (float) $asignacion->monto_acordado : 0.0;
            $montoObjetivo = $montoAcordado > 0 ? $montoAcordado : $salarioBase;

            $recibosEmp = $recibos->get($emp->id, collect([]));
            $cantRecibos = $recibosEmp->count();
            $emitido = (float) $recibosEmp->sum('total_percepciones');
            $faltante = max(0, round($montoObjetivo - $emitido, 2));
            $cubierto = ($faltante <= 0.01);

            // Filtro por pestaña (TODOS, PARCIAL, SIN_RECIBO, COMPLETO)
            if ($this->filtroAuditoria === 'TODOS' && $cubierto) {
                continue;
            }
            if ($this->filtroAuditoria === 'PARCIAL' && ($cantRecibos === 0 || $cubierto)) {
                continue;
            }
            if ($this->filtroAuditoria === 'SIN_RECIBO' && ($cantRecibos > 0 || $cubierto)) {
                continue;
            }
            if ($this->filtroAuditoria === 'COMPLETO' && !$cubierto) {
                continue;
            }

            // Filtro por texto de búsqueda
            if ($search !== '') {
                $nombreCompleto = mb_strtolower($emp->nombre_completo);
                $puesto = mb_strtolower($emp->puesto?->nombre ?? '');
                $area = mb_strtolower($emp->area?->nombre ?? '');
                $idStr = (string) $emp->id;

                if (!str_contains($nombreCompleto, $search) &&
                    !str_contains($puesto, $search) &&
                    !str_contains($area, $search) &&
                    !str_contains($idStr, $search)) {
                    continue;
                }
            }

            $resultado[] = [
                'id_empleado' => $emp->id,
                'empleado' => $emp,
                'nombre_completo' => $emp->nombre_completo,
                'tipo_empleado' => $emp->tipo_empleado,
                'puesto' => $emp->puesto?->nombre ?? 'Sin puesto',
                'area' => $emp->area?->nombre ?? 'Sin área',
                'salario_base' => $salarioBase,
                'monto_acordado' => $montoAcordado,
                'monto_objetivo' => $montoObjetivo,
                'emitido' => $emitido,
                'faltante' => $faltante,
                'cant_recibos' => $cantRecibos,
                'saldo_adelantos' => (float) $emp->saldo_adelantos,
                'porcentaje_cubierto' => $montoObjetivo > 0 ? min(100, round(($emitido / $montoObjetivo) * 100)) : 0,
                'cubierto' => $cubierto,
                'es_asignado' => $asignacion !== null,
                'tiene_recibos' => $cantRecibos > 0,
            ];
        }

        return $resultado;
    }

    public function actualizarMontoAcordado(int $idEmpleado, $nuevoMonto): void
    {
        if (!$this->selectedPeriodoId) {
            return;
        }

        $monto = max(0, (float) $nuevoMonto);
        NominaPeriodoEmpleado::updateOrCreate(
            ['id_periodo' => $this->selectedPeriodoId, 'id_empleado' => $idEmpleado],
            ['monto_acordado' => $monto]
        );

        $empleado = NominaEmpleado::find($idEmpleado);
        $nombre = $empleado ? $empleado->nombre_completo : "#{$idEmpleado}";
        $this->dispatch('toast', message: "Monto acordado para {$nombre} actualizado a $" . number_format($monto, 2), type: 'success');
    }

    public function asignarEmpleadoAPeriodo(int $idEmpleado, $montoAcordado = null): void
    {
        if (!$this->selectedPeriodoId) {
            return;
        }

        $empleado = NominaEmpleado::find($idEmpleado);
        if (!$empleado) {
            $this->dispatch('toast', message: 'Empleado no encontrado.', type: 'error');
            return;
        }

        $monto = ($montoAcordado !== null && $montoAcordado !== '' && (float) $montoAcordado > 0)
            ? (float) $montoAcordado
            : (float) $empleado->salario_base;

        NominaPeriodoEmpleado::updateOrCreate(
            ['id_periodo' => $this->selectedPeriodoId, 'id_empleado' => $idEmpleado],
            ['monto_acordado' => $monto]
        );

        $this->searchAsignarEmpleado = '';
        $this->montoAsignarEmpleado = '';
        $this->dispatch('toast', message: "Empleado {$empleado->nombre_completo} asignado al período con monto $" . number_format($monto, 2), type: 'success');
    }

    public function quitarEmpleadoDePeriodo(int $idEmpleado): void
    {
        if (!$this->selectedPeriodoId) {
            return;
        }

        $tieneRecibos = NominaRecibo::where('id_periodo', $this->selectedPeriodoId)
            ->where('id_empleado', $idEmpleado)
            ->exists();

        if ($tieneRecibos) {
            $this->dispatch('toast', message: 'No se puede remover a un empleado que ya cuenta con recibos en este período.', type: 'warning');
            return;
        }

        NominaPeriodoEmpleado::where('id_periodo', $this->selectedPeriodoId)
            ->where('id_empleado', $idEmpleado)
            ->delete();

        $this->selectedEmpleadosAuditoria = array_values(array_filter($this->selectedEmpleadosAuditoria, fn($id) => (int)$id !== $idEmpleado));

        $this->dispatch('toast', message: 'Empleado removido del período.', type: 'info');
    }

    public function getEmpleadosDisponiblesParaAsignarProperty()
    {
        if (!$this->selectedPeriodoId || empty(trim($this->searchAsignarEmpleado))) {
            return collect([]);
        }

        $asignadosIds = NominaPeriodoEmpleado::where('id_periodo', $this->selectedPeriodoId)->pluck('id_empleado');
        $search = '%' . trim($this->searchAsignarEmpleado) . '%';

        return NominaEmpleado::whereNotIn('id', $asignadosIds)
            ->where('activo', true)
            ->where(function ($q) use ($search) {
                $q->where(DB::raw("CONCAT(nombre, ' ', apellido_paterno, ' ', COALESCE(apellido_materno, ''))"), 'like', $search)
                  ->orWhere('id', 'like', $search);
            })
            ->limit(8)
            ->get();
    }

    public function getTotalSeleccionadosAuditoriaProperty(): float
    {
        if (empty($this->selectedEmpleadosAuditoria)) {
            return 0.0;
        }

        $empleados = $this->obtenerEmpleadosAuditoria();
        $ids = array_map('intval', $this->selectedEmpleadosAuditoria);
        $total = 0.0;
        foreach ($empleados as $item) {
            if (in_array((int) $item['id_empleado'], $ids, true)) {
                $total += (float) $item['faltante'];
            }
        }

        return $total;
    }

    public function iniciarSecuenciaComplementaria(): void
    {
        if (empty($this->selectedEmpleadosAuditoria)) {
            $this->dispatch('toast', message: 'Selecciona al menos un empleado para iniciar la secuencia.', type: 'warning');
            return;
        }

        $this->colaSecuencia = array_values(array_unique(array_map('intval', $this->selectedEmpleadosAuditoria)));
        $this->secuenciaTotal = count($this->colaSecuencia);
        $this->secuenciaIndice = 0;
        $this->enSecuencia = true;
        $this->showAuditoriaModal = false;

        $this->cargarSiguienteEnSecuencia();
    }

    public function cargarSiguienteEnSecuencia(): void
    {
        if (empty($this->colaSecuencia)) {
            $this->enSecuencia = false;
            $this->showReciboModal = false;
            $this->recalcularTotalesPeriodo();
            $this->dispatch('toast', message: '¡Secuencia de recibos complementarios finalizada con éxito!', type: 'success');
            return;
        }

        $idEmpleado = array_shift($this->colaSecuencia);
        $this->secuenciaIndice++;

        $empleado = NominaEmpleado::with(['area', 'puesto'])->find($idEmpleado);
        if (!$empleado) {
            $this->cargarSiguienteEnSecuencia();
            return;
        }

        $this->form->reset();
        $this->isEditingRecibo = false;
        $this->darDeBajaAlFiniquitar = false;
        $this->form->id_periodo = $this->selectedPeriodoId;
        $this->form->id_empleado = $empleado->id;
        $this->empleadoSeleccionado = $empleado;
        $this->empleadoSearch = '';

        // Periodo y Fechas
        $periodo = NominaPeriodo::find($this->selectedPeriodoId);
        if ($periodo) {
            $this->form->fecha_inicio = $periodo->fecha_inicio ? $periodo->fecha_inicio->format('Y-m-d') : '';
            $this->form->fecha_fin = $periodo->fecha_fin ? $periodo->fecha_fin->format('Y-m-d') : '';
            $this->form->fecha_pago = $periodo->fecha_pago ? $periodo->fecha_pago->format('Y-m-d') : now()->toDateString();
        }

        // Recibos previos de este empleado en el período
        $recibosPrevios = NominaRecibo::where('id_periodo', $this->selectedPeriodoId)
            ->where('id_empleado', $empleado->id)
            ->get();

        $asignacion = NominaPeriodoEmpleado::where('id_periodo', $this->selectedPeriodoId)
            ->where('id_empleado', $empleado->id)
            ->first();
        $montoAcordado = $asignacion ? (float) $asignacion->monto_acordado : 0.0;
        $montoObjetivo = $montoAcordado > 0 ? $montoAcordado : (float) $empleado->salario_base;

        $this->form->monto_total_acordado = $montoObjetivo;
        $this->recibosPreviosCountEmpleadoActual = $recibosPrevios->count();
        $this->salarioBaseEmpleadoActual = $montoObjetivo;
        $this->totalEmitidoPrevioEmpleadoActual = (float) $recibosPrevios->sum('total_percepciones');
        $this->saldoPendienteEmpleadoActual = max(0, round($montoObjetivo - $this->totalEmitidoPrevioEmpleadoActual, 2));

        $montoAsignar = $this->saldoPendienteEmpleadoActual > 0 ? $this->saldoPendienteEmpleadoActual : $montoObjetivo;

        // Heredar tipo de recibo y método de pago
        $ultimoRecibo = $recibosPrevios->sortByDesc('id')->first();
        if ($ultimoRecibo) {
            $this->form->tipo_recibo = $ultimoRecibo->tipo_recibo;
            $this->form->metodo_pago = $ultimoRecibo->metodo_pago;
        } else {
            if ($periodo && in_array($periodo->tipo_periodo, ['AGUINALDO', 'FINIQUITO'])) {
                $this->form->tipo_recibo = $periodo->tipo_periodo;
            } else {
                $this->form->tipo_recibo = 'NOMINA';
            }
            $this->form->metodo_pago = $empleado->tipo_pago_predeterminado ?: 'EFECTIVO';
        }

        // Asignar monto a diferencia_efectivo o nomina_fiscal
        if ($this->form->metodo_pago === 'TRANSFERENCIA') {
            $this->form->nomina_fiscal = $montoAsignar;
            $this->form->diferencia_efectivo = 0;
        } else {
            $this->form->nomina_fiscal = 0;
            $this->form->diferencia_efectivo = $montoAsignar;
        }

        // Percepciones adicionales y deducciones en 0.00
        $this->form->horas_extras = 0;
        $this->form->bonos_otros = 0;
        $this->form->monto_aguinaldo = 0;
        $this->form->monto_vacaciones = 0;
        $this->form->monto_prima_vacacional = 0;
        $this->form->descuento_adelanto = 0;
        $this->form->descuento_infonavit = 0;
        $this->form->otros_descuentos = 0;
        $this->form->estado_pago = 'PENDIENTE';

        $numComplemento = $this->recibosPreviosCountEmpleadoActual + 1;
        $this->form->observaciones = $this->recibosPreviosCountEmpleadoActual > 0
            ? "Abono #{$numComplemento} - Saldo restante"
            : null;

        $this->showReciboModal = true;
    }

    public function omitirSiguienteEnSecuencia(): void
    {
        $nombre = $this->empleadoSeleccionado?->nombre_completo ?? 'Empleado';
        $this->dispatch('toast', message: "Se omitió a {$nombre} en la secuencia.", type: 'info');
        $this->cargarSiguienteEnSecuencia();
    }

    public function cancelarSecuencia(): void
    {
        $this->colaSecuencia = [];
        $this->enSecuencia = false;
        $this->showReciboModal = false;
        $this->recalcularTotalesPeriodo();
        $this->dispatch('toast', message: 'Secuencia de recibos complementarios cancelada.', type: 'info');
    }

    public function getDeudaDisponibleProperty(): float
    {
        return $this->form->calcularDeudaDisponible($this->empleadoSeleccionado);
    }

    public function aplicarDescuentoAdelantoSugerido(): void
    {
        $deudaDisponible = $this->deudaDisponible;
        if ($deudaDisponible > 0) {
            $this->form->descuento_adelanto = $deudaDisponible;
            $this->dispatch('toast', message: 'Descuento de anticipo ($' . number_format($deudaDisponible, 2) . ') cargado.', type: 'info');
        }
    }

    public function toggleEstadoPago(int $id): void
    {
        $recibo = NominaRecibo::findOrFail($id);
        if ($recibo->estado_pago === 'PAGADO') {
            $recibo->estado_pago = 'PENDIENTE';
            $recibo->fecha_pago_real = null;
        } else {
            $recibo->estado_pago = 'PAGADO';
            $recibo->fecha_pago_real = now()->toDateString();
        }
        $recibo->save();
        $this->dispatch('toast', message: "Estado del recibo {$recibo->folio} actualizado a {$recibo->estado_pago}.", type: 'success');
    }

    public function confirmDeleteRecibo(int $id): void
    {
        $this->reciboIdToDelete = $id;
        $this->showDeleteReciboModal = true;
    }

    public function deleteRecibo(): void
    {
        if ($this->reciboIdToDelete) {
            $recibo = NominaRecibo::find($this->reciboIdToDelete);
            if ($recibo) {
                $periodo = NominaPeriodo::find($recibo->id_periodo);
                if ($periodo && $periodo->estado === 'CERRADO') {
                    $this->dispatch('toast', message: 'No se puede eliminar un recibo de un período CERRADO.', type: 'error');
                    $this->showDeleteReciboModal = false;
                    $this->reciboIdToDelete = null;
                    return;
                }

                DB::transaction(function () use ($recibo) {
                    $empleado = $recibo->id_empleado ? NominaEmpleado::lockForUpdate()->find($recibo->id_empleado) : null;

                    // Eliminar movimiento contable asociado al recibo si existía
                    NominaMovimientoCuenta::where('id_recibo', $recibo->id)->delete();

                    // Reconciliar en cascada y recalcular el saldo real deudor del empleado según su libro mayor
                    $empleado?->reconciliarKardexSaldos();

                    $recibo->delete();
                });

                if ($recibo->id_periodo) {
                    NominaPeriodo::find($recibo->id_periodo)?->recalcularTotales();
                }
                $this->dispatch('toast', message: 'Recibo eliminado correctamente y saldo deudor sincronizado.', type: 'info');
            }
        }
        $this->showDeleteReciboModal = false;
        $this->reciboIdToDelete = null;
    }

    // --- IMPORTACIÓN EXCEL ---

    public function openImportModal(): void
    {
        if (!$this->selectedPeriodoId) {
            $this->dispatch('toast', message: 'Por favor selecciona un período de nómina primero para importar el archivo.', type: 'warning');
            return;
        }

        $periodo = NominaPeriodo::find($this->selectedPeriodoId);
        if ($periodo && $periodo->estado === 'CERRADO') {
            $this->dispatch('toast', message: 'No se puede importar en un período CERRADO.', type: 'error');
            return;
        }

        $this->archivoExcel = null;
        $this->importErrors = [];
        $this->showImportModal = true;
    }

    public function importarExcel(): void
    {
        if ($this->selectedPeriodoId) {
            $periodo = NominaPeriodo::find($this->selectedPeriodoId);
            if ($periodo && $periodo->estado === 'CERRADO') {
                $this->dispatch('toast', message: 'No se puede importar en un período CERRADO.', type: 'error');
                return;
            }
        }

        $this->validate([
            'archivoExcel' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $import = new NominaPlantillaImport($this->selectedPeriodoId);
            Excel::import($import, $this->archivoExcel);

            $registrados = $import->getRegistradosCount();
            $this->importErrors = $import->getErrors();

            if ($this->selectedPeriodoId) {
                NominaPeriodo::find($this->selectedPeriodoId)?->recalcularTotales();
            }

            $this->dispatch('toast', message: "Se procesaron {$registrados} recibos exitosamente desde el archivo Excel.", type: 'success');

            if (empty($this->importErrors)) {
                $this->showImportModal = false;
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Error en la importación: ' . $e->getMessage(), type: 'error');
        }
    }

    // --- REPORTE CONTABLE DE RECIBOS ---

    public function openReporteModal(): void
    {
        $this->filtroReporteAnio = $this->filtroAnioPeriodo ? (int) $this->filtroAnioPeriodo : (int) now()->year;
        $this->filtroReportePeriodoId = $this->selectedPeriodoId ?: null;
        $this->filtroReporteEmpleadoId = null;
        $this->filtroReporteEmpleadoNombre = null;
        $this->filtroReporteEmpleadoSearch = '';
        $this->filtroReporteMetodoPago = 'TODOS';
        $this->filtroReporteEstadoPago = 'TODOS';
        $this->showReporteModal = true;
    }

    public function seleccionarEmpleadoReporte(int $id, string $nombre): void
    {
        $this->filtroReporteEmpleadoId = $id;
        $this->filtroReporteEmpleadoNombre = "#{$id} - {$nombre}";
        $this->filtroReporteEmpleadoSearch = '';
    }

    public function limpiarEmpleadoReporte(): void
    {
        $this->filtroReporteEmpleadoId = null;
        $this->filtroReporteEmpleadoNombre = null;
        $this->filtroReporteEmpleadoSearch = '';
    }

    public function render()
    {
        // Años disponibles en la base de datos
        $aniosDisponibles = NominaPeriodo::selectRaw('YEAR(fecha_inicio) as anio')
            ->distinct()
            ->whereNotNull('fecha_inicio')
            ->orderBy('anio', 'desc')
            ->pluck('anio')
            ->map(fn($y) => (int)$y)
            ->toArray();

        $currentYear = (int) now()->year;
        if (!in_array($currentYear, $aniosDisponibles)) {
            array_unshift($aniosDisponibles, $currentYear);
        }

        // Períodos filtrados por Año y Tipo
        $periodosQuery = NominaPeriodo::query();

        if (!empty($this->filtroAnioPeriodo)) {
            $periodosQuery->whereYear('fecha_inicio', $this->filtroAnioPeriodo);
        }

        if ($this->filtroTipoPeriodo !== 'TODOS') {
            $periodosQuery->where('tipo_periodo', $this->filtroTipoPeriodo);
        }

        $periodos = $periodosQuery->orderBy('fecha_inicio', 'desc')->get();

        // Período y Auditoría
        $selectedPeriodo = $this->selectedPeriodoId ? NominaPeriodo::find($this->selectedPeriodoId) : null;

        $totalPresupuestoPeriodo = 0.0;
        $totalEmitidoPeriodo = 0.0;
        $totalPendientePeriodo = 0.0;
        $conteoIncompletosPeriodo = 0;
        $empleadosEnPeriodoInfo = [];

        if ($this->selectedPeriodoId && $selectedPeriodo) {
            $tipoPeriodoUpper = strtoupper($selectedPeriodo->tipo_periodo ?? '');
            $esDiario = $tipoPeriodoUpper === 'DIARIO';
            $esFiniquitoOExtraordinario = in_array($tipoPeriodoUpper, ['FINIQUITO', 'EXTRAORDINARIO']);

            $queryPeriodo = NominaEmpleado::with([
                'area',
                'puesto',
                'historialLaboral',
                'asignacionesPeriodos' => fn($q) => $q->where('id_periodo', $selectedPeriodo->id),
            ]);

            if ($esFiniquitoOExtraordinario) {
                $queryPeriodo->where(function ($q) use ($selectedPeriodo) {
                    $q->whereHas('asignacionesPeriodos', fn($aq) => $aq->where('id_periodo', $selectedPeriodo->id))
                      ->orWhereHas('recibos', fn($rq) => $rq->where('id_periodo', $selectedPeriodo->id));
                });
            } else {
                $queryPeriodo->where(function ($q) use ($selectedPeriodo) {
                    $q->laborandoEnPeriodo($selectedPeriodo->fecha_inicio, $selectedPeriodo->fecha_fin)
                      ->orWhereHas('recibos', fn($rq) => $rq->where('id_periodo', $selectedPeriodo->id));
                });
            }

            $empleadosPeriodo = $queryPeriodo->get();
            $recibosPeriodo = NominaRecibo::where('id_periodo', $this->selectedPeriodoId)->get()->groupBy('id_empleado');
            $totalEmitidoPeriodo = (float) ($selectedPeriodo?->total_neto ?? 0);

            foreach ($empleadosPeriodo as $emp) {
                // Determinar el salario base correspondiente a este período
                $ciclo = $emp->historialLaboral->first(function ($c) use ($selectedPeriodo) {
                    return $c->fecha_ingreso <= $selectedPeriodo->fecha_fin && ($c->fecha_baja === null || $c->fecha_baja >= $selectedPeriodo->fecha_inicio);
                });
                $salarioBase = $ciclo ? (float) $ciclo->salario_base : (float) $emp->salario_base;

                $asignacion = $emp->asignacionesPeriodos->first();
                $montoAcordado = $asignacion ? (float) $asignacion->monto_acordado : 0.0;
                $montoObjetivo = $montoAcordado > 0 ? $montoAcordado : $salarioBase;

                if (!$esDiario) {
                    $totalPresupuestoPeriodo += $montoObjetivo;
                }

                $recibosEmp = $recibosPeriodo->get($emp->id, collect([]));
                $cantRecibos = $recibosEmp->count();
                $emitido = (float) $recibosEmp->sum('total_percepciones');
                $faltante = $esDiario ? 0.0 : max(0, round($montoObjetivo - $emitido, 2));

                $empleadosEnPeriodoInfo[$emp->id] = [
                    'cant_recibos' => $cantRecibos,
                    'emitido' => $emitido,
                    'faltante' => $faltante,
                    'salario_base' => $montoObjetivo,
                    'cubierto' => $esDiario || ($faltante <= 0.01),
                ];

                if (!$esDiario && $faltante > 0.01) {
                    $totalPendientePeriodo += $faltante;
                    $conteoIncompletosPeriodo++;
                }
            }
        }

        $empleadosAuditoria = $this->showAuditoriaModal ? $this->obtenerEmpleadosAuditoria() : [];

        // Recibos
        $recibosQuery = NominaRecibo::with(['empleado.area', 'empleado.puesto']);
        if ($this->selectedPeriodoId) {
            $recibosQuery->where('id_periodo', $this->selectedPeriodoId);
        } else {
            // Si no hay período seleccionado, no consultar recibos
            $recibosQuery->whereRaw('1 = 0');
        }

        if ($this->reciboSearch) {
            $search = '%' . trim($this->reciboSearch) . '%';
            $recibosQuery->where(function ($q) use ($search) {
                $q->where('nombre_empleado_snapshot', 'like', $search)
                    ->orWhere('folio', 'like', $search)
                    ->orWhere('id_empleado_snapshot', 'like', $search);
            });
        }

        if ($this->filtroTipoRecibo !== 'TODOS') {
            $recibosQuery->where('tipo_recibo', $this->filtroTipoRecibo);
        }

        if ($this->filtroEstadoPago !== 'TODOS') {
            $recibosQuery->where('estado_pago', $this->filtroEstadoPago);
        }

        if ($this->filtroMetodoPago !== 'TODOS') {
            $recibosQuery->where('metodo_pago', $this->filtroMetodoPago);
        }

        $recibos = $recibosQuery->orderBy('id_empleado_snapshot', 'asc')->paginate(15);

        // Búsqueda de empleados para el autocompletado en el modal (activos primero, pero permitiendo inactivos para finiquitos/extemporáneos)
        $empleadosBusqueda = trim($this->empleadoSearch) !== ''
            ? NominaEmpleado::with(['area', 'puesto', 'historialLaboral'])
                ->where(function ($sq) {
                    $term = '%' . trim($this->empleadoSearch) . '%';
                    $sq->where('nombre', 'like', $term)
                        ->orWhere('apellido_paterno', 'like', $term)
                        ->orWhere('apellido_materno', 'like', $term)
                        ->orWhere('id', 'like', $term)
                        ->orWhereHas('puesto', fn($pq) => $pq->where('nombre', 'like', $term))
                        ->orWhereHas('area', fn($aq) => $aq->where('nombre', 'like', $term));
                })
                ->orderBy('activo', 'desc')
                ->orderBy('nombre', 'asc')
                ->limit(20)
                ->get()
            : collect([]);

        // IDs de empleados que ya tienen un recibo en el período activo
        $empleadosEnPeriodoIds = $this->selectedPeriodoId
            ? NominaRecibo::where('id_periodo', $this->selectedPeriodoId)->pluck('id_empleado')->toArray()
            : [];

        // Períodos disponibles para el modal de reportes contables
        $periodosReporte = $this->filtroReporteAnio
            ? NominaPeriodo::whereYear('fecha_inicio', $this->filtroReporteAnio)->orderBy('fecha_inicio', 'desc')->get()
            : NominaPeriodo::orderBy('fecha_inicio', 'desc')->get();

        // Empleados para el combobox del modal de reportes contables
        $empleadosReporteBusqueda = ($this->showReporteModal && trim($this->filtroReporteEmpleadoSearch) !== '')
            ? NominaEmpleado::with(['area', 'puesto'])
                ->where(function ($sq) {
                    $term = '%' . trim($this->filtroReporteEmpleadoSearch) . '%';
                    $sq->where('nombre', 'like', $term)
                        ->orWhere('apellido_paterno', 'like', $term)
                        ->orWhere('apellido_materno', 'like', $term)
                        ->orWhere('id', 'like', $term);
                })
                ->orderBy('activo', 'desc')
                ->orderBy('nombre', 'asc')
                ->limit(10)
                ->get()
            : collect([]);

        return view('livewire.administracion.nomina.dispersion', [
            'periodos' => $periodos,
            'aniosDisponibles' => $aniosDisponibles,
            'selectedPeriodo' => $selectedPeriodo,
            'recibos' => $recibos,
            'empleadosBusqueda' => $empleadosBusqueda,
            'empleadosEnPeriodoIds' => $empleadosEnPeriodoIds,
            'totalPresupuestoPeriodo' => $totalPresupuestoPeriodo,
            'totalEmitidoPeriodo' => $totalEmitidoPeriodo,
            'totalPendientePeriodo' => $totalPendientePeriodo,
            'conteoIncompletosPeriodo' => $conteoIncompletosPeriodo,
            'empleadosEnPeriodoInfo' => $empleadosEnPeriodoInfo,
            'empleadosAuditoria' => $empleadosAuditoria,
            'periodosReporte' => $periodosReporte,
            'empleadosReporteBusqueda' => $empleadosReporteBusqueda,
        ]);
    }
}

