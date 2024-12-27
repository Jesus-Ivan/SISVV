<?php

namespace App\Imports;

use App\Models\CatalogoVistaVerde;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CatalogoImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $catalogo = CatalogoVistaVerde::where('codigo', $row['codigo'])->first();
            if ($catalogo) {
                $catalogo->update([
                    'nombre' => $row['nombre'],
                    'descripcion' => $row['descripcion'],
                    'id_familia' => $row['id_familia'],
                    'id_categoria' => $row['id_categoria'],
                    'id_proveedor' => $row['id_proveedor'],
                    'costo_unitario' => $row['costo_unitario'],
                    'costo_empleado' => $row['costo_empleado'],
                    'estado' => $row['estado'],
                    'clave_dpto' => $row['clave_dpto'],
                    'tipo' => $row['tipo'],
                    'ultima_compra' => $row['ultima_compra']
                ]);
            } else {
                CatalogoVistaVerde::create([
                    'codigo' => $row['codigo'],
                    'nombre' => $row['nombre'],
                    'descripcion' => $row['descripcion'],
                    'id_familia' => $row['id_familia'],
                    'id_categoria' => $row['id_categoria'],
                    'id_proveedor' => $row['id_proveedor'],
                    'costo_unitario' => $row['costo_unitario'],
                    'costo_empleado' => $row['costo_empleado'],
                    'estado' => $row['estado'],
                    'clave_dpto' => $row['clave_dpto'],
                    'tipo' => $row['tipo'],
                    'ultima_compra' => $row['ultima_compra']
                ]);
            }
        }
    }
}
