<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Dompdf\Dompdf;

class PDFController extends Controller
{
    public function generatePDF(Request $request)
{
    $tiendas = $request->input('tiendas');
    $pedidos = $request->input('pedidos');
    $dataThead = $request->input('dataThead');
    $fecha = $request->input('fecha');
    //$numerosPedido = $request->input('numerosPedido');
    $numerosPedido = array_map(function ($numero) {
        return 'N°' . $numero;
    }, $request->input('numerosPedido'));

    $nombreTiendas = '';
    $numerosOrden = implode(', ', $numerosPedido);

    $filteredDataThead = array_filter($dataThead, function ($element) {
        return $element['id_tienda'] !== 0;
    });
    
    $nombres = array_column($filteredDataThead, 'nombre_tienda');
    $nombreTiendas = implode(', ', $nombres);

    if (count($dataThead) > 2) {
        foreach ($dataThead as &$tienda) {
            $tienda['codigo'] = $tienda['id_tienda'] === 0 ? 'PVP' : $tienda['codigo'];
        }
    } else {
        $addPVP = [
            "id_tienda" => 0,
            "nombre_tienda" => "PVP",
            "codigo" => "PVP"
        ];
    
        array_unshift($dataThead, $addPVP);
    }


    $html = view('pdf.OrdenPDF', compact('tiendas', 'pedidos', 'dataThead', 'fecha', 'nombreTiendas', 'numerosOrden'))->render();

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    //$filename = "Reporte-{$fecha}.pdf";

    return $dompdf->stream();

    /*
    return response($dompdf->output())
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', "attachment; filename={$filename}");*/
}

}
