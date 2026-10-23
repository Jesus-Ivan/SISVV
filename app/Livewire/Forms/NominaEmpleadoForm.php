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
    public ?int $id_area = null;

    #[Validate('nullable|exists:nomina_puestos,id')]
    public ?int $id_puesto = null;

    #[Validate('required|numeric|min:0')]
    public float $salario_base = 0;

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
            'id_area' => $validated['id_area'] ?: null,
            'id_puesto' => $validated['id_puesto'] ?: null,
            'salario_base' => $validated['salario_base'] ?? 0,
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
            'id_area' => $validated['id_area'] ?: null,
            'id_puesto' => $validated['id_puesto'] ?: null,
            'salario_base' => $validated['salario_base'] ?? 0,
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
    }
}

