<?php

namespace App\Livewire\Administracion\Nomina;

use App\Exports\NominaPlantillaExport;
use App\Imports\NominaPlantillaImport;
use App\Livewire\Forms\NominaReciboForm;
use App\Models\NominaEmpleado;
use App\Models\NominaPeriodo;
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

    // Período Activo
    public ?int $selectedPeriodoId = null;
    public string $periodoSearch = '';
    public bool $showPeriodoModal = false;
    public bool $isEditingPeriodo = false;

    // Formulario Período
    public ?int $periodoId = null;
    public string $periodoNombre = '';
    public string $periodoTipo = 'QUINCENAL'; // DIARIO, SEMANAL, QUINCENAL, MENSUAL, AGUINALDO, FINIQUITO, EXTRAORDINARIO
    public string $periodoFechaInicio = '';
    public string $periodoFechaFin = '';
    public string $periodoFechaPago = '';
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

    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        // Seleccionar por defecto el periodo más reciente
        $ultimoPeriodo = NominaPeriodo::orderBy('fecha_inicio', 'desc')->first();
        if ($ultimoPeriodo) {
            $this->selectedPeriodoId = $ultimoPeriodo->id;
        }

        $this->periodoFechaInicio = now()->startOfMonth()->toDateString();
        $this->periodoFechaFin = now()->endOfMonth()->toDateString();
        $this->periodoFechaPago = now()->toDateString();
    }

    public function selectPeriodo(int $id): void
    {
        $this->selectedPeriodoId = $id;
        $this->selectedRecibos = [];
        $this->selectAll = false;
        $this->resetPage();
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
        }
    }

    public function updatedPeriodoFechaInicio($val): void
    {
        if ($this->periodoTipo === 'DIARIO') {
            $this->periodoFechaFin = $val;
            $this->periodoFechaPago = $val;
        }
    }

    public function savePeriodo(): void
    {
        $this->validate([
            'periodoNombre' => 'required|string|max:100',
            'periodoTipo' => 'required|in:DIARIO,SEMANAL,QUINCENAL,MENSUAL,AGUINALDO,FINIQUITO,EXTRAORDINARIO',
            'periodoFechaInicio' => 'required|date',
            'periodoFechaFin' => 'required|date|after_or_equal:periodoFechaInicio',
            'periodoFechaPago' => 'nullable|date',
            'periodoEstado' => 'required|in:BORRADOR,EN_PROCESO,DISPERSADO,CERRADO',
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
            $this->successMessage = "Período '{$p->nombre}' actualizado con éxito.";
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
            $this->selectedPeriodoId = $p->id;
            $this->successMessage = "Período '{$p->nombre}' creado exitosamente.";
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
                // Eliminar recibos asociados
                $p->recibos()->delete();
                $p->delete();
                $this->successMessage = "Período y sus recibos eliminados.";
            }
            $this->selectedPeriodoId = NominaPeriodo::orderBy('fecha_inicio', 'desc')->first()?->id;
        }
        $this->showDeletePeriodoModal = false;
    }

    public function recalcularTotalesPeriodo(): void
    {
        if ($this->selectedPeriodoId) {
            $p = NominaPeriodo::find($this->selectedPeriodoId);
            $p?->recalcularTotales();
            $this->successMessage = "Totales del período recalculados.";
        }
    }

    // --- GESTIÓN DE RECIBOS ---

    public function createRecibo(): void
    {
        $this->form->reset();
        $this->form->id_periodo = $this->selectedPeriodoId;
        $this->form->metodo_pago = 'EFECTIVO';
        $this->form->estado_pago = 'PENDIENTE';
        $this->form->tipo_recibo = 'NOMINA';

        if ($this->selectedPeriodoId) {
            $p = NominaPeriodo::find($this->selectedPeriodoId);
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
    }

    public function onEmpleadoSelected($empleadoId): void
    {
        if (!$empleadoId) {
            return;
        }
        $empleado = NominaEmpleado::find($empleadoId);
        if ($empleado) {
            $this->form->nomina_fiscal = (float) $empleado->salario_base;
            $this->form->metodo_pago = $empleado->tipo_pago_predeterminado ?: 'EFECTIVO';
        }
    }

    public function editRecibo(int $id): void
    {
        $recibo = NominaRecibo::findOrFail($id);
        $this->form->setRecibo($recibo);
        $this->isEditingRecibo = true;
        $this->darDeBajaAlFiniquitar = false;
        $this->showReciboModal = true;
    }

    public function saveRecibo(): void
    {
        try {
            if ($this->isEditingRecibo) {
                $this->form->update();
                $this->successMessage = 'Recibo de nómina actualizado correctamente.';
            } else {
                $recibo = $this->form->store();

                // Si fue finiquito y el usuario optó por dar de baja al empleado
                if ($this->form->tipo_recibo === 'FINIQUITO' && $this->darDeBajaAlFiniquitar && $this->form->id_empleado) {
                    $emp = NominaEmpleado::find($this->form->id_empleado);
                    $emp?->update([
                        'activo' => false,
                        'fecha_baja' => now()->toDateString(),
                    ]);
                    $this->successMessage = 'Recibo de finiquito creado y empleado dado de baja exitosamente.';
                } else {
                    $this->successMessage = 'Recibo de nómina registrado exitosamente.';
                }
            }

            $this->showReciboModal = false;
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->errorMessage = 'Error al procesar recibo: ' . $e->getMessage();
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
        $this->successMessage = "Estado del recibo {$recibo->folio} actualizado a {$recibo->estado_pago}.";
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
                $periodoId = $recibo->id_periodo;
                $recibo->delete();
                if ($periodoId) {
                    NominaPeriodo::find($periodoId)?->recalcularTotales();
                }
                $this->successMessage = "Recibo eliminado correctamente.";
            }
        }
        $this->showDeleteReciboModal = false;
        $this->reciboIdToDelete = null;
    }

    // --- IMPORTACIÓN EXCEL ---

    public function openImportModal(): void
    {
        $this->archivoExcel = null;
        $this->importErrors = [];
        $this->showImportModal = true;
    }

    public function importarExcel(): void
    {
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

            $this->successMessage = "Se procesaron {$registrados} recibos exitosamente desde el archivo Excel.";

            if (empty($this->importErrors)) {
                $this->showImportModal = false;
            }
        } catch (\Exception $e) {
            $this->errorMessage = 'Error en la importación: ' . $e->getMessage();
        }
    }

    public function render()
    {
        // Períodos disponibles
        $periodos = NominaPeriodo::when($this->periodoSearch, function ($q) {
            $q->where('nombre', 'like', '%' . $this->periodoSearch . '%')
                ->orWhere('folio', 'like', '%' . $this->periodoSearch . '%');
        })
            ->orderBy('fecha_inicio', 'desc')
            ->limit(20)
            ->get();

        $selectedPeriodo = $this->selectedPeriodoId ? NominaPeriodo::find($this->selectedPeriodoId) : null;

        // Recibos
        $recibosQuery = NominaRecibo::with(['empleado.area', 'empleado.puesto'])
            ->when($this->selectedPeriodoId, fn($q) => $q->where('id_periodo', $this->selectedPeriodoId));

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

        // Empleados para el select en modal
        $empleadosActivos = NominaEmpleado::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('livewire.administracion.nomina.dispersion', [
            'periodos' => $periodos,
            'selectedPeriodo' => $selectedPeriodo,
            'recibos' => $recibos,
            'empleadosActivos' => $empleadosActivos,
        ]);
    }
}

