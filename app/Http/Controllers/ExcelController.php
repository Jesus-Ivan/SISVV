<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Imports\DataImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function form(){
        return view('registros');
    }

    public function import(Request $request){
        //dd("imp");
        //VALIDAR
        $file = request()->file('file');
        Excel::import(new DataImport, $file);
    }
}
