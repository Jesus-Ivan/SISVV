<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Imports\CatalogoImport;
use App\Imports\CuotasImport;
use App\Imports\EdoCuentaImport;
use App\Imports\IntegrantesImport;
use App\Imports\MembresiasImport;
use App\Imports\SociosImport;
use App\Imports\SociosMembresiasImport;
use App\Imports\TiposCatImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function importData(Request $request)
    {
        $selectedType = $request->input('selectedType');
        $request->validate([
            'file_input' => [
                'required',
                'file',
            ]
        ]);
        // Process import based on selectedType
        switch ($selectedType) {
            case 'socios':
                try {
                    Excel::import(new SociosImport, $request->file('file_input'));
                    return redirect()->back()->with('success', 'SOCIOS REGISTRADOS CORRECTAMENTE EN LA BASE DE DATOS');
                } catch (\Exception $e) {
                    return redirect()->back()->with('fail', 'Error al importar el archivo: ' . $e->getMessage());
                }
                break;
            case 'integrantes':
                try {
                    Excel::import(new IntegrantesImport, $request->file('file_input'));
                    return redirect()->back()->with('success', 'INTEGRANTES REGISTRADOS CORRECTAMENTE EN LA BASE DE DATOS');
                } catch (\Exception $e) {
                    return redirect()->back()->with('fail', 'Error al importar el archivo: ' . $e->getMessage());
                }
                break;
            case 'socios_membresias':
                try {
                    Excel::import(new SociosMembresiasImport, $request->file('file_input'));
                    return redirect()->back()->with('success', 'MEMBRESIAS DE SOCIOS REGISTRADOS CORRECTAMENTE EN LA BASE DE DATOS');
                } catch (\Exception $e) {
                    return redirect()->back()->with('fail', 'Error al importar el archivo: ' . $e->getMessage());
                }
                break;
            case 'estados_cuenta':
                try {
                    Excel::import(new EdoCuentaImport, $request->file('file_input'));
                    return redirect()->back()->with('success', 'ESTADOS DE CUENTA REGISTRADOS CORRECTAMENTE EN LA BASE DE DATOS');
                } catch (\Exception $e) {
                    return redirect()->back()->with('fail', 'Error al importar el archivo: ' . $e->getMessage());
                }
                break;
                case 'cuotas':
                    try {
                        Excel::import(new CuotasImport, $request->file('file_input'));
                        return redirect()->back()->with('success', 'CUOTAS REGISTRADAS CORRECTAMENTE EN LA BASE DE DATOS');
                    } catch (\Exception $e) {
                        return redirect()->back()->with('fail', 'Error al importar el archivo: ' . $e->getMessage());
                    }
                    break;
            case 'membresias':
                try {
                    Excel::import(new MembresiasImport, $request->file('file_input'));
                    return redirect()->back()->with('success', 'MEMBRESIAS REGISTRADAS CORRECTAMENTE EN LA BASE DE DATOS');
                } catch (\Exception $e) {
                    return redirect()->back()->with('fail', 'Error al importar el archivo: ' . $e->getMessage());
                }
                break;
            case 'catalogo':
                try {
                    Excel::import(new CatalogoImport, $request->file('file_input'));
                    return redirect()->back()->with('success', 'CATALOGO REGISTRADO CORRECTAMENTE EN LA BASE DE DATOS');
                } catch (\Exception $e) {
                    return redirect()->back()->with('fail', 'Error al importar el archivo: ' . $e->getMessage());
                }
                break;
            case 'tipos_catalogo':
                try {
                    Excel::import(new TiposCatImport, $request->file('file_input'));
                    return redirect()->back()->with('success', 'TIPOS DE CATALOGO REGISTRADOS CORRECTAMENTE EN LA BASE DE DATOS');
                } catch (\Exception $e) {
                    return redirect()->back()->with('fail', 'Error al importar el archivo: ' . $e->getMessage());
                }
                break;
            default:
                session()->flash('warning', "Tipo de registro no válido");
        }
    }
}
