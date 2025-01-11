<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExcelController extends Controller
{
    public function import() {
        return inertia::render('import');
    }

    public function importExcel(Request $request) {
        dd($request->all());
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        // $path = $request->file('file')->getRealPath();
        // //$data = \Excel::import($path)->get();
        // return back()->with('success', 'Archivo importado correctamente');
    }
}

