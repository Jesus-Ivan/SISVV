<?php

namespace App\Imports;

use App\Models\Socio;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SociosImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        //dd($rows);
        foreach ($rows as $row) {
            $socio = Socio::where('id', $row['id'])->first();
            if ($socio) {
                $socio->update([
                    'nombre' => $row['nombre'],
                    'apellido_p' => $row['apellido_p'],
                    'apellido_m' => $row['apellido_m'],
                    'fecha_registro' => $row['fecha_registro'],
                    'estado_civil' => $row['estado_civil'],
                    'calle' => $row['calle'],
                    'num_exterior' => $row['num_exterior'],
                    'num_interior' => $row['num_interior'],
                    'colonia' => $row['colonia'],
                    'ciudad' => $row['ciudad'],
                    'estado' => $row['estado'],
                    'codigo_postal' => $row['codigo_postal'],
                    'tel_1' => $row['tel_1'],
                    'tel_2' => $row['tel_2'],
                    'correo1' => $row['correo1'],
                    'correo2' => $row['correo2'],
                    'correo3' => $row['correo3'],
                    'correo4' => $row['correo4'],
                    'correo5' => $row['correo5'],
                    'curp' => $row['curp'],
                    'rfc' => $row['rfc']
                ]);
            } else {
                Socio::create([
                    'id' => $row['id'],
                    'nombre' => $row['nombre'],
                    'apellido_p' => $row['apellido_p'],
                    'apellido_m' => $row['apellido_m'],
                    'fecha_registro' => $row['fecha_registro'],
                    'estado_civil' => $row['estado_civil'],
                    'calle' => $row['calle'],
                    'num_exterior' => $row['num_exterior'],
                    'num_interior' => $row['num_interior'],
                    'colonia' => $row['colonia'],
                    'ciudad' => $row['ciudad'],
                    'estado' => $row['estado'],
                    'codigo_postal' => $row['codigo_postal'],
                    'tel_1' => $row['tel_1'],
                    'tel_2' => $row['tel_2'],
                    'correo1' => $row['correo1'],
                    'correo2' => $row['correo2'],
                    'correo3' => $row['correo3'],
                    'correo4' => $row['correo4'],
                    'correo5' => $row['correo5'],
                    'curp' => $row['curp'],
                    'rfc' => $row['rfc']
                ]);
            }
        }
    }
}
