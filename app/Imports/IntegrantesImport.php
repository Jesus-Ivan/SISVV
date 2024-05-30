<?php

namespace App\Imports;

use App\Models\IntegrantesSocio;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class IntegrantesImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $integrante = IntegrantesSocio::where('id', $row['id'])->first();
            if ($integrante) {
                $integrante->update([
                    'id_socio' => $row['id_socio'],
                    'nombre_integrante' => $row['nombre_integrante'],
                    'apellido_p_integrante' => $row['apellido_p_integrante'],
                    'apellido_m_integrante' => $row['apellido_m_integrante'],
                    'img_path_integrante' => $row['img_path_integrante'],
                    'fecha_nac' => $row['fecha_nac'],
                    'parentesco' => $row['parentesco'],
                ]);
            } else {
                IntegrantesSocio::create([
                    'id' => $row['id'],
                    'id_socio' => $row['id_socio'],
                    'nombre_integrante' => $row['nombre_integrante'],
                    'apellido_p_integrante' => $row['apellido_p_integrante'],
                    'apellido_m_integrante' => $row['apellido_m_integrante'],
                    'img_path_integrante' => $row['img_path_integrante'],
                    'fecha_nac' => $row['fecha_nac'],
                    'parentesco' => $row['parentesco'],
                ]);
            }
        }
    }
}
