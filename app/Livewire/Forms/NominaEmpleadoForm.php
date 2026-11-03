<?php

namespace App\Livewire\Forms;

use App\Models\NominaEmpleado;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Form;

class NominaEmpleadoForm extends Form
{
    public ?NominaEmpleado $empleado = null;

    #[Validate('required|string|min:2|max:100')]
    public string $nombre = '';

    #[Validate('required|string|min:2|max:100')]
    public string $apellido_paterno = '';

    #[Validate('nullable|string|max:100')]
    public ?string $apellido_materno = null;

    #[Validate('required|in:PLANTA,TEMPORAL')]
    public string $tipo_empleado = 'PLANTA';

    #[Validate('nullable|exists:nomina_areas,id')]
    public int|string|null $id_area = null;

    #[Validate('nullable|exists:nomina_puestos,id')]
    public int|string|null $id_puesto = null;

    #[Validate('required|numeric|min:0', message: [
        'required' => 'El salario base es obligatorio.',
        'numeric' => 'El salario base debe ser un número válido.',
        'min' => 'El salario base no puede ser negativo.'
    ])]
    public float|int|string|null $salario_base = 0;

    #[Validate('required|in:EFECTIVO,TRANSFERENCIA,MIXTO')]
    public string $tipo_pago_predeterminado = 'EFECTIVO';

    #[Validate('nullable|date')]
    public ?string $fecha_ingreso = null;

    #[Validate('nullable|date')]
    public ?string $fecha_baja = null;

    #[Validate('boolean')]
    public bool $activo = true;

    #[Validate('nullable|image|max:3072')]
    public $foto = null;

    #[Validate('nullable|string|max:25')]
    public ?string $telefono = null;

    #[Validate('nullable|string|max:20')]
    public ?string $rfc = null;

    #[Validate('nullable|string|max:25')]
    public ?string $curp = null;

    #[Validate('nullable|string|max:25')]
    public ?string $nss = null;

    public function setEmpleado(NominaEmpleado $empleado): void
    {
        $this->empleado = $empleado;
        $this->nombre = $empleado->nombre;
        $this->apellido_paterno = $empleado->apellido_paterno;
        $this->apellido_materno = $empleado->apellido_materno;
        $this->tipo_empleado = $empleado->tipo_empleado;
        $this->id_area = $empleado->id_area;
        $this->id_puesto = $empleado->id_puesto;
        $this->salario_base = (float) $empleado->salario_base;
        $this->tipo_pago_predeterminado = $empleado->tipo_pago_predeterminado;
        $this->fecha_ingreso = $empleado->fecha_ingreso ? $empleado->fecha_ingreso->format('Y-m-d') : null;
        $this->fecha_baja = $empleado->fecha_baja ? $empleado->fecha_baja->format('Y-m-d') : null;
        $this->activo = (bool) $empleado->activo;
        $this->telefono = $empleado->telefono;
        $this->rfc = $empleado->rfc;
        $this->curp = $empleado->curp;
        $this->nss = $empleado->nss;
        $this->foto = null;
    }

    public function store(): NominaEmpleado
    {
        $validated = $this->validate();

        $fotoPath = null;
        if ($this->foto) {
            $fotoPath = $this->foto->store('empleados', 'public');
        }

        $empleado = NominaEmpleado::create([
            'nombre' => $validated['nombre'],
            'apellido_paterno' => $validated['apellido_paterno'],
            'apellido_materno' => $validated['apellido_materno'],
            'tipo_empleado' => $validated['tipo_empleado'],
            'id_area' => !empty($validated['id_area']) ? (int) $validated['id_area'] : null,
            'id_puesto' => !empty($validated['id_puesto']) ? (int) $validated['id_puesto'] : null,
            'salario_base' => (float) ($validated['salario_base'] ?? 0),
            'tipo_pago_predeterminado' => $validated['tipo_pago_predeterminado'],
            'fecha_ingreso' => $validated['fecha_ingreso'] ?: now()->toDateString(),
            'fecha_baja' => $validated['fecha_baja'] ?: null,
            'activo' => $validated['activo'] ?? true,
            'foto_path' => $fotoPath,
            'telefono' => $validated['telefono'] ?: null,
            'rfc' => $validated['rfc'] ? strtoupper($validated['rfc']) : null,
            'curp' => $validated['curp'] ? strtoupper($validated['curp']) : null,
            'nss' => $validated['nss'] ?: null,
            'saldo_adelantos' => 0,
        ]);

        // Crear ciclo inicial en el historial laboral
        $empleado->historialLaboral()->create([
            'fecha_ingreso' => $empleado->fecha_ingreso ? $empleado->fecha_ingreso->format('Y-m-d') : ($validated['fecha_ingreso'] ?: now()->toDateString()),
            'fecha_baja' => $empleado->fecha_baja ? $empleado->fecha_baja->format('Y-m-d') : ($validated['fecha_baja'] ?: null),
            'salario_base' => $empleado->salario_base,
            'id_puesto' => $empleado->id_puesto,
            'id_area' => $empleado->id_area,
            'tipo_empleado' => $empleado->tipo_empleado,
            'motivo_reingreso' => 'Alta inicial de empleado',
            'motivo_baja' => $empleado->fecha_baja ? 'Baja registrada en alta inicial' : null,
        ]);

        $this->reset();
        return $empleado;
    }

    public function update(): void
    {
        if (!$this->empleado) {
            return;
        }

        $validated = $this->validate();

        $data = [
            'nombre' => $validated['nombre'],
            'apellido_paterno' => $validated['apellido_paterno'],
            'apellido_materno' => $validated['apellido_materno'],
            'tipo_empleado' => $validated['tipo_empleado'],
            'id_area' => !empty($validated['id_area']) ? (int) $validated['id_area'] : null,
            'id_puesto' => !empty($validated['id_puesto']) ? (int) $validated['id_puesto'] : null,
            'salario_base' => (float) ($validated['salario_base'] ?? 0),
            'tipo_pago_predeterminado' => $validated['tipo_pago_predeterminado'],
            'fecha_ingreso' => $validated['fecha_ingreso'] ?: null,
            'fecha_baja' => $validated['fecha_baja'] ?: null,
            'activo' => $validated['activo'] ?? true,
            'telefono' => $validated['telefono'] ?: null,
            'rfc' => $validated['rfc'] ? strtoupper($validated['rfc']) : null,
            'curp' => $validated['curp'] ? strtoupper($validated['curp']) : null,
            'nss' => $validated['nss'] ?: null,
        ];

        if ($this->foto) {
            if ($this->empleado->foto_path && Storage::disk('public')->exists($this->empleado->foto_path)) {
                Storage::disk('public')->delete($this->empleado->foto_path);
            }
            $data['foto_path'] = $this->foto->store('empleados', 'public');
        }

        $this->empleado->update($data);

        // Sincronizar datos relevantes con el ciclo activo actual si existe
        $cicloActivo = $this->empleado->cicloLaboralActivo;
        if ($cicloActivo) {
            $cicloActivo->update([
                'salario_base' => (float) ($validated['salario_base'] ?? 0),
                'id_puesto' => !empty($validated['id_puesto']) ? (int) $validated['id_puesto'] : null,
                'id_area' => !empty($validated['id_area']) ? (int) $validated['id_area'] : null,
                'tipo_empleado' => $validated['tipo_empleado'],
                'fecha_ingreso' => $validated['fecha_ingreso'] ?: $cicloActivo->fecha_ingreso,
                'fecha_baja' => $validated['fecha_baja'] ?: null,
            ]);
        }
    }
}

