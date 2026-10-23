<?php

namespace App\Exports;

use App\Exports\Sheets\Socios\IntegrantesSheet;
use App\Exports\Sheets\Socios\SociosSheet;
use App\Models\Socio;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SociosExport implements WithMultipleSheets
{
    protected Collection $socios;

    public function __construct()
    {
        $this->socios = Socio::with(['socioMembresias', 'integrantesSocio'])
            ->whereHas('socioMembresia')
            ->get();
    }

    public function sheets(): array
    {
        return [
            new SociosSheet($this->socios),
            new IntegrantesSheet($this->socios),
        ];
    }
}
