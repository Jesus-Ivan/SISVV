<?php

namespace App\Livewire\Administracion\Nomina;

use App\Models\NominaArea;
use App\Models\NominaPuesto;
use Livewire\Component;

class Catalogos extends Component
{
    public string $activeTab = 'areas'; // 'areas' o 'puestos'

    // Formulario Área
    public bool $showAreaModal = false;
    public bool $isEditingArea = false;
    public ?int $areaId = null;
    public string $areaNombre = '';
    public ?string $areaDescripcion = null;
    public bool $areaActivo = true;

    // Formulario Puesto
    public bool $showPuestoModal = false;
    public bool $isEditingPuesto = false;
    public ?int $puestoId = null;
    public string $puestoNombre = '';
    public ?int $puestoIdArea = null;
    public float $puestoSalarioSugerido = 0;
    public ?string $puestoDescripcion = null;
    public bool $puestoActivo = true;

    public string $successMessage = '';
    public string $errorMessage = '';

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    // --- MÉTODOS ÁREA ---
    public function createArea(): void
    {
        $this->resetErrorBag();
        $this->areaId = null;
        $this->areaNombre = '';
        $this->areaDescripcion = '';
        $this->areaActivo = true;
        $this->isEditingArea = false;
        $this->showAreaModal = true;
    }

    public function editArea(int $id): void
    {
        $this->resetErrorBag();
        $area = NominaArea::findOrFail($id);
        $this->areaId = $area->id;
        $this->areaNombre = $area->nombre;
        $this->areaDescripcion = $area->descripcion;
        $this->areaActivo = (bool) $area->activo;
        $this->isEditingArea = true;
        $this->showAreaModal = true;
    }

    public function saveArea(): void
    {
        $this->validate([
            'areaNombre' => 'required|string|min:2|max:100',
            'areaDescripcion' => 'nullable|string|max:255',
            'areaActivo' => 'boolean',
        ]);

        if ($this->isEditingArea && $this->areaId) {
            $area = NominaArea::findOrFail($this->areaId);
            $area->update([
                'nombre' => $this->areaNombre,
                'descripcion' => $this->areaDescripcion,
                'activo' => $this->areaActivo,
            ]);
            $this->successMessage = 'Área actualizada con éxito.';
        } else {
            NominaArea::create([
                'nombre' => $this->areaNombre,
                'descripcion' => $this->areaDescripcion,
                'activo' => $this->areaActivo,
            ]);
            $this->successMessage = 'Área registrada con éxito.';
        }

        $this->showAreaModal = false;
    }

    public function toggleAreaActivo(int $id): void
    {
        $area = NominaArea::findOrFail($id);
        $area->activo = !$area->activo;
        $area->save();
        $this->successMessage = 'Estado del área actualizado.';
    }

    public function deleteArea(int $id): void
    {
        $area = NominaArea::withCount(['empleados', 'puestos'])->findOrFail($id);
        if ($area->empleados_count > 0 || $area->puestos_count > 0) {
            $this->errorMessage = 'No se puede eliminar el área porque tiene puestos o empleados asignados. Puedes desactivarla en su lugar.';
            return;
        }

        $area->delete();
        $this->successMessage = 'Área eliminada exitosamente.';
    }

    // --- MÉTODOS PUESTO ---
    public function createPuesto(): void
    {
        $this->resetErrorBag();
        $this->puestoId = null;
        $this->puestoNombre = '';
        $this->puestoIdArea = null;
        $this->puestoSalarioSugerido = 0;
        $this->puestoDescripcion = '';
        $this->puestoActivo = true;
        $this->isEditingPuesto = false;
        $this->showPuestoModal = true;
    }

    public function editPuesto(int $id): void
    {
        $this->resetErrorBag();
        $puesto = NominaPuesto::findOrFail($id);
        $this->puestoId = $puesto->id;
        $this->puestoNombre = $puesto->nombre;
        $this->puestoIdArea = $puesto->id_area;
        $this->puestoSalarioSugerido = (float) $puesto->salario_sugerido;
        $this->puestoDescripcion = $puesto->descripcion;
        $this->puestoActivo = (bool) $puesto->activo;
        $this->isEditingPuesto = true;
        $this->showPuestoModal = true;
    }

    public function savePuesto(): void
    {
        $this->validate([
            'puestoNombre' => 'required|string|min:2|max:100',
            'puestoIdArea' => 'nullable|exists:nomina_areas,id',
            'puestoSalarioSugerido' => 'required|numeric|min:0',
            'puestoDescripcion' => 'nullable|string|max:255',
            'puestoActivo' => 'boolean',
        ]);

        if ($this->isEditingPuesto && $this->puestoId) {
            $puesto = NominaPuesto::findOrFail($this->puestoId);
            $puesto->update([
                'nombre' => $this->puestoNombre,
                'id_area' => $this->puestoIdArea ?: null,
                'salario_sugerido' => $this->puestoSalarioSugerido,
                'descripcion' => $this->puestoDescripcion,
                'activo' => $this->puestoActivo,
            ]);
            $this->successMessage = 'Puesto actualizado correctamente.';
        } else {
            NominaPuesto::create([
                'nombre' => $this->puestoNombre,
                'id_area' => $this->puestoIdArea ?: null,
                'salario_sugerido' => $this->puestoSalarioSugerido,
                'descripcion' => $this->puestoDescripcion,
                'activo' => $this->puestoActivo,
            ]);
            $this->successMessage = 'Puesto registrado exitosamente.';
        }

        $this->showPuestoModal = false;
    }

    public function togglePuestoActivo(int $id): void
    {
        $puesto = NominaPuesto::findOrFail($id);
        $puesto->activo = !$puesto->activo;
        $puesto->save();
        $this->successMessage = 'Estado del puesto actualizado.';
    }

    public function deletePuesto(int $id): void
    {
        $puesto = NominaPuesto::withCount('empleados')->findOrFail($id);
        if ($puesto->empleados_count > 0) {
            $this->errorMessage = 'No se puede eliminar el puesto porque tiene empleados asignados. Puedes desactivarlo.';
            return;
        }

        $puesto->delete();
        $this->successMessage = 'Puesto eliminado exitosamente.';
    }

    public function render()
    {
        $areas = NominaArea::withCount(['puestos', 'empleados'])->orderBy('nombre')->get();
        $puestos = NominaPuesto::with(['area'])->withCount('empleados')->orderBy('nombre')->get();

        return view('livewire.administracion.nomina.catalogos', [
            'areas' => $areas,
            'puestos' => $puestos,
        ]);
    }
}

