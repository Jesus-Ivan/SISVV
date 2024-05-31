<?php

namespace App\Imports;

use App\Models\TiposCatalogo;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TiposCatImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $tipo_catalogo = TiposCatalogo::where('id', $row['id'])->first();
            if ($tipo_catalogo) {
                $tipo_catalogo->update([
                    'codigo_catalogo' => $row['codigo_catalogo'],
                    'clave_tipo' => $row['clave_tipo']
                ]);
            } else {
                TiposCatalogo::create([
                    'id' => $row['id'],
                    'codigo_catalogo' => $row['codigo_catalogo'],
                    'clave_tipo' => $row['clave_tipo']
                ]);
            }    
        }  
    }
}
