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
                    'id_familia' => $row['id_familia'],
                    'id_categoria' => $row['id_categoria'],
                    'id_unidad' => $row['id_unidad'],
                    'id_proveedor' => $row['id_proveedor'],
                    'stock_amc' => $row['stock_amc'],
                    'st_min_amc' => $row['st_min_amc'],
                    'st_max_amc' => $row['st_max_amc'],
                    'stock_bar' => $row['stock_bar'],
                    'st_min_bar' => $row['st_min_bar'],
                    'st_max_bar' => $row['st_max_bar'],
                    'stock_barra' => $row['stock_barra'],
                    'st_min_barra' => $row['st_min_barra'],
                    'st_max_barra' => $row['st_max_barra'],
                    'stock_caddie' => $row['stock_caddie'],
                    'st_min_caddie' => $row['st_min_caddie'],
                    'st_max_caddie' => $row['st_max_caddie'],
                    'stock_caf' => $row['stock_caf'],
                    'st_min_caf' => $row['st_min_caf'],
                    'st_max_caf' => $row['st_max_caf'],
                    'stock_lock' => $row['stock_lock'],
                    'costo_unitario' => $row['costo_unitario'],
                    'costo_empleado' => $row['costo_empleado'],
                    'estado' => $row['estado']
                ]);
            } else {
                CatalogoVistaVerde::create([
                    'codigo' => $row['codigo'],
                    'nombre' => $row['nombre'],
                    'id_familia' => $row['id_familia'],
                    'id_categoria' => $row['id_categoria'],
                    'id_unidad' => $row['id_unidad'],
                    'id_proveedor' => $row['id_proveedor'],
                    'stock_amc' => $row['stock_amc'],
                    'st_min_amc' => $row['st_min_amc'],
                    'st_max_amc' => $row['st_max_amc'],
                    'stock_bar' => $row['stock_bar'],
                    'st_min_bar' => $row['st_min_bar'],
                    'st_max_bar' => $row['st_max_bar'],
                    'stock_barra' => $row['stock_barra'],
                    'st_min_barra' => $row['st_min_barra'],
                    'st_max_barra' => $row['st_max_barra'],
                    'stock_caddie' => $row['stock_caddie'],
                    'st_min_caddie' => $row['st_min_caddie'],
                    'st_max_caddie' => $row['st_max_caddie'],
                    'stock_caf' => $row['stock_caf'],
                    'st_min_caf' => $row['st_min_caf'],
                    'st_max_caf' => $row['st_max_caf'],
                    'stock_lock' => $row['stock_lock'],
                    'costo_unitario' => $row['costo_unitario'],
                    'costo_empleado' => $row['costo_empleado'],
                    'estado' => $row['estado']
                ]);
            }
        }
    }
}
