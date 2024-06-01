<?php

namespace App\Imports;

use App\Models\SocioMembresia;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SociosMembresiasImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $membresia = SocioMembresia::where('id', $row['id'])->first();
            if ($membresia) {
                $membresia->update([
                    'id_socio' => $row['id_socio'],
                    'clave_membresia' => $row['clave_membresia'],
                    'estado' => $row['estado']
                ]);
            } else {
                SocioMembresia::create([
                    'id' => $row['id'],
                    'id_socio' => $row['id_socio'],
                    'clave_membresia' => $row['clave_membresia'],
                    'estado' => $row['estado']
                ]);
            }
        }
    }
}
