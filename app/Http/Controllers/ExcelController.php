<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Imports\ProductsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Inertia\Inertia;

class ExcelController extends Controller
{
    public function importarProductosExcel(Request $request) {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $archivoExcel = $request->file('file');

        $cabecerasEsperadas = ['plus', 'nombre'];
        $cabecerasExcel = $this->ObtenerNombresEncabezados($archivoExcel);

        $tieneCabeceras = $this->ValidarExstenciaEncabezados($cabecerasEsperadas, $cabecerasExcel);

        if ($tieneCabeceras['coincideCabeceras'] === false) {
            return redirect()->back()->withError($tieneCabeceras['mensaje']);
        }

        Excel::import(new ProductsImport, $archivoExcel);
    }

    private function ObtenerNombresEncabezados($archivo) {
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($archivo);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();
        $headers = $sheetData[0];
        return $headers;
    }

    private function ValidarExstenciaEncabezados($cabecerasEsperadas, $cabecerasExcel) {
        $diferencias = array_diff($this->NormalizarCabeceras($cabecerasEsperadas), $this->NormalizarCabeceras($cabecerasExcel));
    
        if (!empty($diferencias)) {
            return [
                'coincideCabeceras' => false,
                'mensaje' => 'El archivo no contiene la/s siguiente/s columna/s: ' . implode(', ', $diferencias)
            ];
        }
    
        return ['coincideCabeceras' => true];
    }

    private function NormalizarCabeceras($cabeceras) {
        return array_map(function ($cabecera) {
            return strtolower(preg_replace('/[^A-Za-z0-9_]/', '', $cabecera));
        }, $cabeceras);
    }
}

