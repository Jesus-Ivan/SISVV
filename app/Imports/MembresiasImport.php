<?php

namespace App\Imports;

use App\Models\Membresias;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MembresiasImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $membresia = Membresias::where('clave', $row['clave'])->first();
            if ($membresia) {
                $membresia->update([
                    'descripcion' => $row['descripcion'],
                ]);
            } else {
                Membresias::create([
                    'clave' => $row['clave'],
                    'descripcion' => $row['descripcion'],
                ]);
            }
        }
    }
}
