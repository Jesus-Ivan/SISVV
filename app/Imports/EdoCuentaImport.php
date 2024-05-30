<?php

namespace App\Imports;

use App\Models\EstadoCuenta;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EdoCuentaImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $edo_cuenta = EstadoCuenta::where('id', $row['id'])->first();
            if ($edo_cuenta) {
                $edo_cuenta->update([
                    'id_venta_pago' => $row['id_venta_pago'],
                    'folio_evento' => $row['folio_evento'],
                    'id_cuota' => $row['id_cuota'],
                    'id_socio' => $row['id_socio'],
                    'concepto' => $row['concepto'],
                    'fecha' => $row['fecha'],
                    'cargo' => $row['cargo'],
                    'abono' => $row['abono'],
                    'saldo' => $row['saldo'],
                    'saldo_favor' => $row['saldo_favor'],
                    'consumo' => $row['consumo'],
                    'created_at' => $row['created_at'],
                    'updated_at' => $row['updated_at']
                ]);
            } else {
                EstadoCuenta::create([
                    'id' => $row['id'],
                    'id_venta_pago' => $row['id_venta_pago'],
                    'folio_evento' => $row['folio_evento'],
                    'id_cuota' => $row['id_cuota'],
                    'id_socio' => $row['id_socio'],
                    'concepto' => $row['concepto'],
                    'fecha' => $row['fecha'],
                    'cargo' => $row['cargo'],
                    'abono' => $row['abono'],
                    'saldo' => $row['saldo'],
                    'saldo_favor' => $row['saldo_favor'],
                    'consumo' => $row['consumo'],
                    'created_at' => $row['created_at'],
                    'updated_at' => $row['updated_at']
                ]);
            }
        }
    }
}
