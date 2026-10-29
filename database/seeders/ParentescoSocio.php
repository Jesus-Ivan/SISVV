<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParentescoSocio extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $parentescos = [
            'Padre',
            'Madre',
            'Esposo',
            'Esposa',
            'Hijo/a',
            'Hermano/a',
            'Yerno/nuera',
            'Sobrino/a',
            'Nieto/a',
            'Tio/a',
            'Suegro/a',
        ];
        $aux = [];

        foreach ($parentescos as $key => $value) {
            array_push($aux, [
                'parentesco' => $value,
                'estado' => true,
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }

        // Inserción masiva por bloques (chunks) para optimizar memoria
        foreach (array_chunk($aux, 100) as $chunk) {
            DB::table('parentesco_socios')->insert($chunk);
        }
    }
}
