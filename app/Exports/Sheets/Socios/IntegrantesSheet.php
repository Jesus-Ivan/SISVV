<?php

namespace App\Exports\Sheets\Socios;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class IntegrantesSheet implements FromArray, WithTitle
{
    public function __construct(protected Collection $socios) {}

    public function array(): array
    {
        $integrantes = [[
            'id_socio' => 'ID SOCIO',
            'nombre_socio' => 'NOMBRE SOCIO',
            'nombre_integrante' => 'NOMBRE INTEGRANTE',
            'parentesco' => 'PARENTESCO'
        ]];

        foreach ($this->socios as $socio) {
            foreach ($socio->integrantesSocio as $integrante) {
                $integrantes[] = [
                    'id_socio' => $socio->id,
                    'nombre_socio' => trim($socio->nombre . ' ' . $socio->apellido_p . ' ' . $socio->apellido_m),
                    'nombre_integrante' => trim(
                        $integrante->nombre_integrante . ' ' .
                        $integrante->apellido_p_integrante . ' ' .
                        $integrante->apellido_m_integrante
                    ),
                    'parentesco' => $integrante->parentesco
                ];
            }
        }

        return $integrantes;
    }

    public function title(): string
    {
        return 'Integrantes';
    }
}
