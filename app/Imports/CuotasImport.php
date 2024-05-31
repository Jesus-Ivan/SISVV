<?php

namespace App\Imports;

use App\Models\Cuota;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CuotasImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $cuota = Cuota::where('id', $row['id'])->first();
            if ($cuota) {
                $cuota->update([
                    'descripcion' => $row['descripcion'],
                    'monto' => $row['monto'],
                    'tipo' => $row['tipo'],
                    'clave_membresia' => $row['clave_membresia']
                ]);
            } else {
                Cuota::create([
                    'id' => $row['id'],
                    'descripcion' => $row['descripcion'],
                    'monto' => $row['monto'],
                    'tipo' => $row['tipo'],
                    'clave_membresia' => $row['clave_membresia']
                ]);
            }
        }  
    }
}
