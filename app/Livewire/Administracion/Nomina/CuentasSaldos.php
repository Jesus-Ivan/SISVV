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

            $empleado->update(['saldo_adelantos' => $saldoNuevo]);

            return $movimiento;
        });

        $this->dispatch('toast', message: "Movimiento {$movimientoCreado->folio} registrado exitosamente.", type: 'success');

        // Si fue adelanto o abono en efectivo, disparar apertura del comprobante para imprimir en nueva pestaña
        if (in_array($movimientoCreado->tipo_movimiento, ['ADELANTO_EFECTIVO', 'ABONO_DIRECTO'])) {
            $this->dispatch('abrir-pdf', url: route('nomina.pdf.movimiento', $movimientoCreado->id));
        }

        $this->showMovimientoModal = false;
    }

    public function verHistorial(int $empleadoId): void
    {
        $this->historialEmpleadoId = $empleadoId;
        $this->historialEmpleado = NominaEmpleado::with('area', 'puesto')->findOrFail($empleadoId);
        $this->showHistorialModal = true;
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

        // Movimientos del empleado si el modal de historial está abierto
        $historialMovimientos = [];
        if ($this->showHistorialModal && $this->historialEmpleadoId) {
            $historialMovimientos = NominaMovimientoCuenta::with(['usuario', 'recibo', 'periodo'])
                ->where('id_empleado', $this->historialEmpleadoId)
                ->orderBy('fecha', 'desc')
                ->orderBy('id', 'desc')
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
        ]);
    }
}

