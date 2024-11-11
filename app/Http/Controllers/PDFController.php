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

    foreach ($tiendas as &$tienda) {
        $tienda['codigo'] = $tienda['id_tienda'] === 0 ? 'PVP' : $tienda['codigo'];
    }

    $html = view('pdf.OrdenPDF', compact('tiendas', 'pedidos', 'dataThead'))->render();

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $filename = "Reporte-{$fecha}.pdf";

    return response($dompdf->output())
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', "attachment; filename={$filename}");
}

}
