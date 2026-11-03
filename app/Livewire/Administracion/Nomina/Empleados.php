<?php

namespace App\Livewire\Administracion\Nomina;

use App\Livewire\Forms\NominaEmpleadoForm;
use App\Models\NominaArea;
use App\Models\NominaEmpleado;
use App\Models\NominaPuesto;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Empleados extends Component
{
    use WithPagination, WithFileUploads;

    public NominaEmpleadoForm $form;

    public string $search = '';
    public string $tipo_empleado = 'PLANTA'; // Default PLANTA
    public string $filtro_activo = '1';     // '1': Activos, '0': Inactivos, 'todos': Todos
    public ?int $filtro_area = null;
    public ?int $filtro_puesto = null;

    public bool $showModal = false;
    public bool $isEditing = false;
    public bool $showDeleteModal = false;
    public ?int $empleadoIdToDelete = null;
    public ?string $empleadoNameToDelete = null;
    public string $errorMessage = '';
    public string $successMessage = '';

    // Modal Reingreso
    public bool $showReingresoModal = false;
    public ?NominaEmpleado $empleadoParaReingreso = null;
    public string $reingresoFecha = '';
    public float|int|string $reingresoSalario = 0;
    public ?int $reingresoPuestoId = null;
    public ?int $reingresoAreaId = null;
    public string $reingresoTipo = 'PLANTA';
    public string $reingresoMotivo = 'Recontratación';

    // Modal Baja
    public bool $showBajaModal = false;
    public ?NominaEmpleado $empleadoParaBaja = null;
    public string $bajaFecha = '';
    public string $bajaMotivo = 'Renuncia voluntaria';

    // Modal Historial Laboral
    public bool $showHistorialLaboralModal = false;
    public ?NominaEmpleado $empleadoHistorial = null;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTipoEmpleado(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroActivo(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroArea(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroPuesto(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->form->reset();
        $this->form->id_area = null;
        $this->form->id_puesto = null;
        $this->form->salario_base = 0;
        $this->form->tipo_empleado = $this->tipo_empleado === 'TEMPORAL' ? 'TEMPORAL' : 'PLANTA';
        $this->form->tipo_pago_predeterminado = 'EFECTIVO';
        $this->form->activo = true;
        $this->form->fecha_ingreso = now()->toDateString();
        $this->isEditing = false;
        $this->errorMessage = '';
        $this->showModal = true;
    }

    public function updatedFormIdArea($value): void
    {
        if (empty($value)) {
            $this->form->id_area = null;
        }
        $this->form->id_puesto = null;
    }

    public function edit(int $id): void
    {
        $empleado = NominaEmpleado::findOrFail($id);
        $this->form->setEmpleado($empleado);
        $this->isEditing = true;
        $this->errorMessage = '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->errorMessage = '';
        try {
            if ($this->isEditing) {
                $this->form->update();
                $this->dispatch('toast', message: 'Empleado actualizado correctamente.', type: 'success');
            } else {
                $this->form->store();
                $this->dispatch('toast', message: 'Empleado registrado exitosamente.', type: 'success');
            }

            $this->showModal = false;
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->errorMessage = 'Error al guardar el empleado: ' . $e->getMessage();
            $this->dispatch('toast', message: $this->errorMessage, type: 'error');
        }
    }

    // --- REINGRESO ---

    public function openReingresoModal(int $id): void
    {
        $emp = NominaEmpleado::with(['area', 'puesto'])->findOrFail($id);
        $this->empleadoParaReingreso = $emp;
        $this->reingresoFecha = now()->toDateString();
        $this->reingresoSalario = (float) $emp->salario_base;
        $this->reingresoPuestoId = $emp->id_puesto;
        $this->reingresoAreaId = $emp->id_area;
        $this->reingresoTipo = $emp->tipo_empleado ?: 'PLANTA';
        $this->reingresoMotivo = 'Recontratación / Nueva temporada';
        $this->showReingresoModal = true;
    }

    public function updatedReingresoAreaId($val): void
    {
        if (empty($val)) {
            $this->reingresoAreaId = null;
        }
        $this->reingresoPuestoId = null;
    }

    public function saveReingreso(): void
    {
        $this->validate([
            'reingresoFecha' => 'required|date',
            'reingresoSalario' => 'required|numeric|min:0',
            'reingresoPuestoId' => 'nullable|exists:nomina_puestos,id',
            'reingresoAreaId' => 'nullable|exists:nomina_areas,id',
            'reingresoTipo' => 'required|in:PLANTA,TEMPORAL',
            'reingresoMotivo' => 'nullable|string|max:255',
        ]);

        if (!$this->empleadoParaReingreso) {
            return;
        }

        $emp = $this->empleadoParaReingreso;
        $emp->update([
            'activo' => true,
            'fecha_ingreso' => $this->reingresoFecha,
            'fecha_baja' => null,
            'salario_base' => (float) $this->reingresoSalario,
            'id_puesto' => $this->reingresoPuestoId,
            'id_area' => $this->reingresoAreaId,
            'tipo_empleado' => $this->reingresoTipo,
        ]);

        $emp->historialLaboral()->create([
            'fecha_ingreso' => $this->reingresoFecha,
            'salario_base' => (float) $this->reingresoSalario,
            'id_puesto' => $this->reingresoPuestoId,
            'id_area' => $this->reingresoAreaId,
            'tipo_empleado' => $this->reingresoTipo,
            'motivo_reingreso' => $this->reingresoMotivo ?: 'Recontratación',
        ]);

        $this->showReingresoModal = false;
        $this->empleadoParaReingreso = null;
        $this->dispatch('toast', message: "Reingreso de '{$emp->nombre_completo}' registrado exitosamente con nuevo ciclo laboral.", type: 'success');
    }

    // --- BAJA ---

    public function openBajaModal(int $id): void
    {
        $emp = NominaEmpleado::findOrFail($id);
        $this->empleadoParaBaja = $emp;
        $this->bajaFecha = now()->toDateString();
        $this->bajaMotivo = 'Renuncia voluntaria';
        $this->showBajaModal = true;
    }

    public function saveBaja(): void
    {
        $this->validate([
            'bajaFecha' => 'required|date',
            'bajaMotivo' => 'nullable|string|max:255',
        ]);

        if (!$this->empleadoParaBaja) {
            return;
        }

        $emp = $this->empleadoParaBaja;
        $emp->update([
            'activo' => false,
            'fecha_baja' => $this->bajaFecha,
        ]);

        $cicloActivo = $emp->cicloLaboralActivo;
        if ($cicloActivo) {
            $cicloActivo->update([
                'fecha_baja' => $this->bajaFecha,
                'motivo_baja' => $this->bajaMotivo ?: 'Baja de empleado',
            ]);
        }

        $this->showBajaModal = false;
        $this->empleadoParaBaja = null;
        $this->dispatch('toast', message: "Empleado '{$emp->nombre_completo}' dado de baja exitosamente.", type: 'success');
    }

    public function toggleActivo(int $id): void
    {
        $empleado = NominaEmpleado::findOrFail($id);
        if ($empleado->activo) {
            $this->openBajaModal($id);
        } else {
            $this->openReingresoModal($id);
        }
    }

    // --- HISTORIAL LABORAL ---

    public function openHistorialLaboral(int $id): void
    {
        $this->empleadoHistorial = NominaEmpleado::with([
            'historialLaboral' => fn($q) => $q->orderBy('fecha_ingreso', 'desc')->orderBy('id', 'desc'),
            'historialLaboral.puesto',
            'historialLaboral.area',
            'puesto',
            'area'
        ])->findOrFail($id);
        $this->showHistorialLaboralModal = true;
    }

    public function closeHistorialLaboralModal(): void
    {
        $this->showHistorialLaboralModal = false;
        $this->empleadoHistorial = null;
    }

    public function confirmDelete(int $id): void
    {
        $empleado = NominaEmpleado::findOrFail($id);
        $this->empleadoIdToDelete = $id;
        $this->empleadoNameToDelete = $empleado->nombre_completo;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if (!$this->empleadoIdToDelete) {
            return;
        }

        $empleado = NominaEmpleado::find($this->empleadoIdToDelete);
        if ($empleado) {
            $tieneRecibos = $empleado->recibos()->exists();
            $tieneMovimientos = $empleado->movimientosCuenta()->exists();

            if ($tieneRecibos || $tieneMovimientos) {
                // Si tiene historial, desactivar en vez de eliminar para no romper integridad
                $empleado->update([
                    'activo' => false,
                    'fecha_baja' => now()->toDateString(),
                ]);
                $ciclo = $empleado->cicloLaboralActivo;
                if ($ciclo) {
                    $ciclo->update([
                        'fecha_baja' => now()->toDateString(),
                        'motivo_baja' => 'Baja por eliminación con historial',
                    ]);
                }
                $this->dispatch('toast', message: "El empleado tiene historial de nómina o cuentas, por lo que fue desactivado en lugar de eliminarse permanentemente.", type: 'warning');
            } else {
                if ($empleado->foto_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($empleado->foto_path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($empleado->foto_path);
                }
                $empleado->delete();
                $this->dispatch('toast', message: 'Empleado eliminado con éxito.', type: 'info');
            }
        }

        $this->showDeleteModal = false;
        $this->empleadoIdToDelete = null;
        $this->empleadoNameToDelete = null;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->errorMessage = '';
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->empleadoIdToDelete = null;
        $this->empleadoNameToDelete = null;
    }

    public function render()
    {
        $query = NominaEmpleado::with(['area', 'puesto'])
            ->withCount('recibos');

        if ($this->search) {
            $search = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', $search)
                    ->orWhere('apellido_paterno', 'like', $search)
                    ->orWhere('apellido_materno', 'like', $search)
                    ->orWhere('rfc', 'like', $search)
                    ->orWhere('curp', 'like', $search)
                    ->orWhere('id', 'like', $search);
            });
        }

        if ($this->tipo_empleado !== 'TODOS') {
            $query->where('tipo_empleado', $this->tipo_empleado);
        }

        if ($this->filtro_activo === '1') {
            $query->where('activo', true);
        } elseif ($this->filtro_activo === '0') {
            $query->where('activo', false);
        }

        if ($this->filtro_area) {
            $query->where('id_area', $this->filtro_area);
        }

        if ($this->filtro_puesto) {
            $query->where('id_puesto', $this->filtro_puesto);
        }

        $empleados = $query->orderBy('activo', 'desc')
            ->orderBy('nombre', 'asc')
            ->paginate(15);

        $areas = NominaArea::where('activo', true)->orderBy('nombre')->get();
        $formAreaId = !empty($this->form->id_area) ? $this->form->id_area : null;
        $puestos = NominaPuesto::where('activo', true)
            ->when($formAreaId, fn($q) => $q->where('id_area', $formAreaId))
            ->orderBy('nombre')
            ->get();

        $puestosReingreso = NominaPuesto::where('activo', true)
            ->when($this->reingresoAreaId, fn($q) => $q->where('id_area', $this->reingresoAreaId))
            ->orderBy('nombre')
            ->get();

        return view('livewire.administracion.nomina.empleados', [
            'empleados' => $empleados,
            'areas' => $areas,
            'puestos' => $puestos,
            'puestosReingreso' => $puestosReingreso,
        ]);
    }
}

