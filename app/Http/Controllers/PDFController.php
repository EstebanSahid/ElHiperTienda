<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Dompdf\Dompdf;

class PDFController extends Controller
{
    private function generarCabeceraPDF(Request $request): array {
        $dataThead = $request->input('dataThead');
        $usuarioGenera = $request->user()->name;
        $numerosPedido = array_map(fn($n) => 'N°' . $n, $request->input('numerosPedido', []));
        $numerosOrden = implode(', ', $numerosPedido);

        $filtered = array_filter($dataThead, fn($e) => $e['id_tienda'] !== 0);
        $nombres = array_column($filtered, 'nombre_tienda');
        $nombreTiendas = implode(', ', $nombres);

        if (count($dataThead) > 2) {
            foreach ($dataThead as &$tienda) {
                $tienda['codigo'] = $tienda['id_tienda'] === 0 ? 'PVP' : $tienda['codigo'];
            }
        } else {
            array_unshift($dataThead, [
                "id_tienda" => 0,
                "nombre_tienda" => "PVP",
                "codigo" => "PVP"
            ]);
        }

        return [
            'fecha' => $request->input('fecha'),
            'nombreTiendas' => $nombreTiendas,
            'numerosOrden' => $numerosOrden,
            'usuarioGenera' => $usuarioGenera,
            'dataThead' => $dataThead,
        ];
    }

    public function generatePDF(Request $request)
    {
        $tiendas = $request->input('tiendas');
        $pedidos = $request->input('pedidos');
        $dataThead = $request->input('dataThead');
        $fecha = $request->input('fecha');
        $usuarioGenera = $request->user()->name;

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


        $html = view('pdf.OrdenPDF', compact('tiendas', 'pedidos', 'dataThead', 'fecha', 'nombreTiendas', 'numerosOrden', 'usuarioGenera'))->render();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream();
    }

}
