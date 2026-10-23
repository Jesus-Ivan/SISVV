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
        $this->form->tipo_empleado = $this->tipo_empleado === 'TEMPORAL' ? 'TEMPORAL' : 'PLANTA';
        $this->form->tipo_pago_predeterminado = 'EFECTIVO';
        $this->form->activo = true;
        $this->form->fecha_ingreso = now()->toDateString();
        $this->isEditing = false;
        $this->errorMessage = '';
        $this->showModal = true;
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
                $this->successMessage = 'Empleado actualizado correctamente.';
            } else {
                $this->form->store();
                $this->successMessage = 'Empleado registrado exitosamente.';
            }

            $this->showModal = false;
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->errorMessage = 'Error al guardar el empleado: ' . $e->getMessage();
        }
    }

    public function toggleActivo(int $id): void
    {
        $empleado = NominaEmpleado::findOrFail($id);
        $empleado->activo = !$empleado->activo;
        if (!$empleado->activo && !$empleado->fecha_baja) {
            $empleado->fecha_baja = now()->toDateString();
        } elseif ($empleado->activo) {
            $empleado->fecha_baja = null;
        }
        $empleado->save();

        $this->successMessage = "Estado del empleado '{$empleado->nombre_completo}' actualizado.";
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
                $this->successMessage = "El empleado tiene historial de nómina o cuentas, por lo que fue desactivado en lugar de eliminarse permanentemente.";
            } else {
                if ($empleado->foto_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($empleado->foto_path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($empleado->foto_path);
                }
                $empleado->delete();
                $this->successMessage = "Empleado eliminado con éxito.";
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
        $puestos = NominaPuesto::where('activo', true)
            ->when($this->form->id_area, fn($q) => $q->where('id_area', $this->form->id_area))
            ->orderBy('nombre')
            ->get();

        return view('livewire.administracion.nomina.empleados', [
            'empleados' => $empleados,
            'areas' => $areas,
            'puestos' => $puestos,
        ]);
    }
}

