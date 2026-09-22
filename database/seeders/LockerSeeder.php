<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LockerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $lockers = [];

        // Generar 300 lockers de Damas
        for ($i = 1; $i <= 300; $i++) {
            $lockers[] = [
                'numero' => $i,
                'seccion' => 'DAMAS',
                'estado_actual' => 'DISPONIBLE',
                'id_socio_actual' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Generar 300 lockers de Caballeros
        for ($i = 1; $i <= 300; $i++) {
            $lockers[] = [
                'numero' => $i,
                'seccion' => 'CABALLEROS',
                'estado_actual' => 'DISPONIBLE',
                'id_socio_actual' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Inserción masiva por bloques (chunks) para optimizar memoria
        foreach (array_chunk($lockers, 100) as $chunk) {
            DB::table('locker')->insert($chunk);
        }
    }
}
