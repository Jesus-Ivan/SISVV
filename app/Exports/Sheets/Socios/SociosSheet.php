<?php

namespace App\Exports\Sheets\Socios;

use App\Models\Cuota;
use App\Models\Socio;
use App\Models\SocioCuota;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class SociosSheet implements FromArray, WithTitle
{
    public function __construct(protected Collection $socios) {}

    public function array(): array
    {
        $final[] = [
            'id' => 'ID',
            'nombre' => 'NOMBRE',
            'estado' => 'ESTADO',
            'membresias' => 'MEMBRESIAS CONTRATADAS',
            'tarifa_especial' => 'TARIFA PERSONALIZADA',
            'locker' => 'LOCKERS',
            'resguardo' => 'RESGUARDO CARRITO',
            'membresia' => 'MEMBRESIA',
            'integrantes' => 'INTEGRANTES',
        ];

        $cuotas_locker = Cuota::where('tipo', 'like', '%LOC%')->get()->toArray();
        $cuotas_resg_carr = Cuota::where('tipo', 'like', '%RES%')->get()->toArray();
        $cuotas_membresias = Cuota::whereNotNull('clave_membresia')->get()->toArray();

        DB::transaction(function () use ($cuotas_locker, $cuotas_resg_carr, $cuotas_membresias, &$final) {
            foreach ($this->socios as $socio) {
                $cuotas = SocioCuota::with('cuota')->where('id_socio', $socio->id)->get();

                $final[] = [
                    'id' => $socio->id,
                    'nombre' => $socio->nombre . ' ' . $socio->apellido_p . ' ' . $socio->apellido_m,
                    'estado' => $socio->socioMembresias->pluck('estado')->implode(', '),
                    'membresias' => $this->listarMembresias($socio),
                    'tarifa_especial' => $cuotas->whereNotNull('monto_personalizado')->isNotEmpty() ? 'SÍ' : 'NO',
                    'locker' => $this->sumar_cuotas(array_column($cuotas_locker, 'id'), $cuotas),
                    'resguardo' => $this->sumar_cuotas(array_column($cuotas_resg_carr, 'id'), $cuotas),
                    'membresia' => $this->sumar_cuotas(array_column($cuotas_membresias, 'id'), $cuotas),
                    'integrantes' => $socio->integrantesSocio->count(),
                ];
            }
        }, 2);

        return $final;
    }

    public function title(): string
    {
        return 'Socios';
    }

    private function sumar_cuotas(array $id_cuotas_permitidas, $cuotas)
    {
        $suma = 0;
        $cuotas_filtradas = $cuotas->whereIn('id_cuota', $id_cuotas_permitidas);

        foreach ($cuotas_filtradas as $filtrada) {
            $suma += $filtrada->monto_a_cobrar;
        }

        return $suma;
    }

    private function listarMembresias(Socio $socio): string
    {
        return $socio->socioMembresias
            ->pluck('clave_membresia')
            ->unique()
            ->implode(', ');
    }
}
