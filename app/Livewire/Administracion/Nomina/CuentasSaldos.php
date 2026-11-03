<?php

namespace App\Livewire\Administracion\Nomina;

use App\Models\NominaEmpleado;
use App\Models\NominaMovimientoCuenta;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class CuentasSaldos extends Component
{
    use WithPagination;

    public string $search = '';
    public string $tipo_empleado = 'TODOS';
    public bool $soloConSaldo = false;

    // Modal para registrar movimiento manual (Adelanto o Abono en Efectivo)
    public bool $showMovimientoModal = false;
    public ?int $empleadoIdSeleccionado = null;
    public ?NominaEmpleado $empleadoSeleccionado = null;
    public string $tipo_movimiento = 'ADELANTO_EFECTIVO'; // ADELANTO_EFECTIVO, ABONO_DIRECTO, AJUSTE
    public float $monto = 0;
    public string $fecha = '';
    public ?string $referencia = null;

    // Modal Historial / Kardex del empleado
    public bool $showHistorialModal = false;
    public ?int $historialEmpleadoId = null;
    public ?NominaEmpleado $historialEmpleado = null;

    // Filtros del modal Kardex
    public string $kardexSearch = '';
    public string $kardexTipoMovimiento = 'TODOS';
    public string $kardexFechaInicio = '';
    public string $kardexFechaFin = '';

    // Modal para Reporte a Excel de Cuentas y Movimientos
    public bool $showReporteExcelModal = false;
    public ?int $exportEmpleadoId = null;
    public ?string $exportEmpleadoNombre = null;
    public string $exportEmpleadoSearch = '';
    public string $exportTipoEmpleado = 'TODOS';
    public string $exportTipoMovimiento = 'TODOS';
    public string $exportFechaInicio = '';
    public string $exportFechaFin = '';

    public string $successMessage = '';
    public string $errorMessage = '';

    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $this->fecha = now()->toDateString();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTipoEmpleado(): void
    {
        $this->resetPage();
    }

    public function updatingSoloConSaldo(): void
    {
        $this->resetPage();
    }

    public function openMovimientoModal(?int $empleadoId = null): void
    {
        $this->resetErrorBag();
        $this->empleadoIdSeleccionado = $empleadoId;
        $this->empleadoSeleccionado = $empleadoId ? NominaEmpleado::find($empleadoId) : null;
        $this->tipo_movimiento = 'ADELANTO_EFECTIVO';
        $this->monto = 0;
        $this->fecha = now()->toDateString();
        $this->referencia = '';
        $this->showMovimientoModal = true;
    }

    public function updatedEmpleadoIdSeleccionado($id): void
    {
        $this->empleadoSeleccionado = $id ? NominaEmpleado::find($id) : null;
    }

    public function registrarMovimiento(): void
    {
        $minMonto = $this->tipo_movimiento === 'AJUSTE' ? 0 : 0.01;

        $this->validate([
            'empleadoIdSeleccionado' => 'required|exists:nomina_empleados,id',
            'tipo_movimiento' => 'required|in:ADELANTO_EFECTIVO,ABONO_DIRECTO,AJUSTE',
            'monto' => "required|numeric|min:{$minMonto}",
            'fecha' => 'required|date',
            'referencia' => 'nullable|string|max:255',
        ]);

        $movimientoCreado = DB::transaction(function () {
            $empleado = NominaEmpleado::lockForUpdate()->findOrFail($this->empleadoIdSeleccionado);
            $saldoAnterior = (float) $empleado->saldo_adelantos;
            $montoCapturado = (float) $this->monto;

            if ($this->tipo_movimiento === 'ADELANTO_EFECTIVO') {
                $naturaleza = 'CARGO';
                $montoOperacion = $montoCapturado;
                $saldoNuevo = $saldoAnterior + $montoOperacion;
            } elseif ($this->tipo_movimiento === 'ABONO_DIRECTO') {
                $naturaleza = 'ABONO';
                $montoOperacion = $montoCapturado;
                $saldoNuevo = max(0, $saldoAnterior - $montoOperacion);
            } else { // AJUSTE (Monto representa el nuevo saldo objetivo deseado)
                $nuevoSaldoObjetivo = max(0, $montoCapturado);
                $diferencia = round($nuevoSaldoObjetivo - $saldoAnterior, 2);

                if ($diferencia >= 0) {
                    $naturaleza = 'CARGO';
                    $montoOperacion = $diferencia;
                } else {
                    $naturaleza = 'ABONO';
                    $montoOperacion = abs($diferencia);
                }
                $saldoNuevo = $nuevoSaldoObjetivo;
            }

            $folioData = NominaMovimientoCuenta::generarSiguienteFolio($empleado->id, $this->tipo_movimiento);

            $referenciaDefault = match ($this->tipo_movimiento) {
                'ADELANTO_EFECTIVO' => 'Adelanto en efectivo otorgado',
                'ABONO_DIRECTO' => 'Abono directo en efectivo',
                'AJUSTE' => 'Ajuste manual de saldo deudor',
                default => 'Movimiento de cuenta contable',
            };

            $movimiento = NominaMovimientoCuenta::create([
                'id_empleado' => $empleado->id,
                'folio' => $folioData['folio'],
                'consecutivo_empleado' => $folioData['consecutivo'],
                'tipo_movimiento' => $this->tipo_movimiento,
                'naturaleza' => $naturaleza,
                'monto' => $montoOperacion,
                'saldo_anterior' => $saldoAnterior,
                'saldo_nuevo' => $saldoNuevo,
                'fecha' => $this->fecha,
                'referencia' => $this->referencia ?: $referenciaDefault,
                'id_usuario' => auth()->id(),
            ]);

            $empleado->reconciliarKardexSaldos();

            return $movimiento;
        });

        $urlPdf = route('nomina.pdf.movimiento', $movimientoCreado->id);

        $actionText = match ($movimientoCreado->tipo_movimiento) {
            'ADELANTO_EFECTIVO' => '🖨️ Imprimir Vale de Anticipo',
            'ABONO_DIRECTO' => '🖨️ Imprimir Recibo de Abono',
            'AJUSTE' => '🖨️ Imprimir Comprobante de Ajuste',
            default => '🖨️ Imprimir Comprobante PDF',
        };

        $this->dispatch('toast', 
            message: "Movimiento {$movimientoCreado->folio} registrado exitosamente.", 
            type: 'success',
            actionUrl: $urlPdf,
            actionText: $actionText
        );

        // Disparar apertura automática del comprobante para imprimir en nueva pestaña
        $this->dispatch('abrir-pdf', url: $urlPdf);

        $this->showMovimientoModal = false;
    }

    public function verHistorial(int $empleadoId): void
    {
        $this->historialEmpleadoId = $empleadoId;
        $this->historialEmpleado = NominaEmpleado::with('area', 'puesto')->findOrFail($empleadoId);
        $this->limpiarFiltrosKardex();
        $this->showHistorialModal = true;
    }

    public function limpiarFiltrosKardex(): void
    {
        $this->kardexSearch = '';
        $this->kardexTipoMovimiento = 'TODOS';
        $this->kardexFechaInicio = '';
        $this->kardexFechaFin = '';
    }

    public function reconciliarCuenta(int $empleadoId): void
    {
        $empleado = NominaEmpleado::findOrFail($empleadoId);
        $nuevoSaldo = $empleado->reconciliarKardexSaldos();
        $this->historialEmpleado = $empleado->fresh(['area', 'puesto']);
        $this->dispatch('toast', 
            message: "Kardex de {$empleado->nombre_completo} reconciliado exitosamente. Saldo deudor: $" . number_format($nuevoSaldo, 2), 
            type: 'success'
        );
    }

    public function openReporteExcelModal(): void
    {
        $this->exportEmpleadoId = null;
        $this->exportEmpleadoNombre = null;
        $this->exportEmpleadoSearch = '';
        $this->exportTipoEmpleado = 'TODOS';
        $this->exportTipoMovimiento = 'TODOS';
        $this->exportFechaInicio = '';
        $this->exportFechaFin = '';
        $this->showReporteExcelModal = true;
    }

    public function seleccionarExportEmpleado(int $id, string $nombre): void
    {
        $this->exportEmpleadoId = $id;
        $this->exportEmpleadoNombre = "#{$id} - {$nombre}";
        $this->exportEmpleadoSearch = '';
    }

    public function limpiarExportEmpleado(): void
    {
        $this->exportEmpleadoId = null;
        $this->exportEmpleadoNombre = null;
        $this->exportEmpleadoSearch = '';
    }

    public function render()
    {
        // Métricas Globales
        $totalSaldoPendiente = (float) NominaEmpleado::sum('saldo_adelantos');
        $adelantosEsteMes = (float) NominaMovimientoCuenta::where('tipo_movimiento', 'ADELANTO_EFECTIVO')
            ->whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->sum('monto');
        $descontadoEnNominaMes = (float) NominaMovimientoCuenta::where('tipo_movimiento', 'DESCUENTO_NOMINA')
            ->whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->sum('monto');

        // Query Empleados
        $query = NominaEmpleado::with(['area', 'puesto']);

        if ($this->search) {
            $search = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', $search)
                    ->orWhere('apellido_paterno', 'like', $search)
                    ->orWhere('apellido_materno', 'like', $search)
                    ->orWhere('id', 'like', $search);
            });
        }

        if ($this->tipo_empleado !== 'TODOS') {
            $query->where('tipo_empleado', $this->tipo_empleado);
        }

        if ($this->soloConSaldo) {
            $query->where('saldo_adelantos', '>', 0);
        }

        $empleados = $query->orderBy('saldo_adelantos', 'desc')
            ->orderBy('nombre', 'asc')
            ->paginate(15);

        // Movimientos del empleado con filtros si el modal de historial está abierto
        $historialMovimientos = [];
        if ($this->showHistorialModal && $this->historialEmpleadoId) {
            $queryHistorial = NominaMovimientoCuenta::with(['usuario', 'recibo', 'periodo'])
                ->where('id_empleado', $this->historialEmpleadoId);

            if ($this->kardexTipoMovimiento !== 'TODOS') {
                $queryHistorial->where('tipo_movimiento', $this->kardexTipoMovimiento);
            }

            if ($this->kardexFechaInicio) {
                $queryHistorial->whereDate('fecha', '>=', $this->kardexFechaInicio);
            }

            if ($this->kardexFechaFin) {
                $queryHistorial->whereDate('fecha', '<=', $this->kardexFechaFin);
            }

            if (trim($this->kardexSearch) !== '') {
                $searchK = '%' . trim($this->kardexSearch) . '%';
                $queryHistorial->where(function ($q) use ($searchK) {
                    $q->where('folio', 'like', $searchK)
                        ->orWhere('referencia', 'like', $searchK);
                });
            }

            $historialMovimientos = $queryHistorial->orderBy('fecha', 'desc')
                ->orderBy('id', 'desc')
                ->get();
        }

        // Sugerencias de búsqueda de empleado para modal de reporte Excel
        $empleadosExportBusqueda = collect();
        if ($this->showReporteExcelModal && trim($this->exportEmpleadoSearch) !== '') {
            $searchEmp = '%' . trim($this->exportEmpleadoSearch) . '%';
            $empleadosExportBusqueda = NominaEmpleado::where(function ($q) use ($searchEmp) {
                $q->where('nombre', 'like', $searchEmp)
                    ->orWhere('apellido_paterno', 'like', $searchEmp)
                    ->orWhere('apellido_materno', 'like', $searchEmp)
                    ->orWhere('id', 'like', $searchEmp);
            })
            ->orderBy('nombre')
            ->limit(10)
            ->get();
        }

        $todosEmpleadosActivos = NominaEmpleado::where('activo', true)->orderBy('nombre')->get();

        return view('livewire.administracion.nomina.cuentas-saldos', [
            'empleados' => $empleados,
            'totalSaldoPendiente' => $totalSaldoPendiente,
            'adelantosEsteMes' => $adelantosEsteMes,
            'descontadoEnNominaMes' => $descontadoEnNominaMes,
            'historialMovimientos' => $historialMovimientos,
            'todosEmpleadosActivos' => $todosEmpleadosActivos,
            'empleadosExportBusqueda' => $empleadosExportBusqueda,
        ]);
    }
}

