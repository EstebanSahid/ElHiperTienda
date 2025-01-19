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
    private function ObtenerNombresEncabezados($archivo, $hoja) {
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

        $NombreHojasArray = $reader->load($archivoExcel)->getSheetNames();
        $NombreHojas = $this->GenerarClaveValorHojasExcel($NombreHojasArray);
    
        return response()->json(['hojas' => $NombreHojas], 200);
    }

    private function GenerarClaveValorHojasExcel($NombreHojasArray) {
        $HojasExcel = array_map(function ($hoja, $index) {
            return [
                'id_hoja' => $index,
                'nombre_hoja' => $hoja
            ];
        }, $NombreHojasArray, array_keys($NombreHojasArray));

        return $HojasExcel;
    }

    /* IMPORTAR PRODUCTOS DESDE EXCEL */
    public function importarProductosExcel(Request $request) {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'hoja' => 'required'
        ]);

        $archivoExcel = $request->file('file');
        $hoja = $request->hoja;

        $cabecerasEsperadas = ['plus', 'nombre'];
        $cabecerasExcel = $this->ObtenerNombresEncabezados($archivoExcel, $hoja);
        $tieneCabeceras = $this->ValidarExstenciaEncabezados($cabecerasEsperadas, $cabecerasExcel);
        
        if ($tieneCabeceras['coincideCabeceras'] === false) {
            return redirect()->back()->withError($tieneCabeceras['mensaje']);
        }

        $this->Importar(new ProductsImport($request->user()->id, $cabecerasEsperadas), $archivoExcel, 'products', 'Productos importados exitosamente');
    }
}

