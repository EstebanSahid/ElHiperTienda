<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Imports\ProductsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use inertia\Inertia;

class ExcelController extends Controller
{
    /* FUNCIONES GLOBALES */
    private function ObtenerNombresEncabezados($archivo) {
        $reader = new Xlsx();
        $spreadsheet = $reader->load($archivo);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();
        return $sheetData[0];
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

    private function Importar($modelo, $archivoExcel, $interfaz, $mensaje) {
        try
        {
            Excel::import($modelo, $archivoExcel);

            $noRegistrados = $modelo->getNoRegistrados();
            $errores = $modelo->getErrores();

            if (!empty($noRegistrados)) {
                return redirect()->route($interfaz)->with([
                    'success' => $noRegistrados, 
                    'duracionNotificacion' => 10]);
            }

            return redirect()->route($interfaz)->with([
                'success' => $mensaje
            ]);
        }
        catch (\Exception $e)
        {
            return redirect()->back()->withError($e->getMessage());

            return redirect()->back()->withErrors([
                'error' => $e->getMessage(),
                'duracionNotificacion' => 5,
            ]);
        }   
    }   

    /* OBTENER HOJAS DEL EXCEL */
    public function ObtenerHojasExcel(Request $request) {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);
    
        $archivoExcel = $request->file('file');
        $reader = new Xlsx();
        $spreadsheet = $reader->load($archivoExcel);
        $sheetNames = $spreadsheet->getSheetNames();
    
        return response()->json(['hojas' => $sheetNames], 200);
    }

    

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
        // dd($cabecerasExcel);

        $this->Importar(new ProductsImport($request->user()->id, $cabecerasEsperadas), $archivoExcel, 'products', 'Productos importados exitosamente');
    }
}

