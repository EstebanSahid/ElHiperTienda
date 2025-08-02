<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
    /* Reporte por pedido*/
    public function generarPDFPorPedido(Request $request, $idPedido)
    {
        $cabecera = $this->obtenerCabeceraOrden($idPedido);
        $pedidos = $this->obtenerDetallesOrden($idPedido);
        
        $fecha = $cabecera->fecha;
        $nombreTiendas = $cabecera->tienda;
        $numerosOrden = 'N° ' . str_pad($cabecera->id_pedido, 5, '0', STR_PAD_LEFT);
        $usuarioGenera = $request->user()->name;

        $dataThead = [
            // [
            //     "id_tienda" => $cabecera->id_tienda,
            //     "nombre_tienda" => $cabecera->tienda,
            //     "codigo" => $cabecera->codigo
            // ],
            [
                "id_tienda" => 0,
                "nombre_tienda" => "PVP",
                "codigo" => "PVP"
            ]
        ];


        $html = view('pdf.OrdenPDF', compact('fecha', 'nombreTiendas', 'numerosOrden', 'usuarioGenera', 'dataThead', 'pedidos'))->render();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream();
    }

    private function obtenerCabeceraOrden($idPedido){
        return DB::table('pedidos as p')
                ->join('tienda as t', 'p.id_tienda', '=', 't.id_tienda')
                ->select(
                    'p.id_pedido', 
                    'p.fecha_pedido as fecha', 
                    'p.id_tienda', 
                    't.nombre as tienda',
                    't.codigo'
                )
                ->where('p.id_pedido', '=', $idPedido)
                ->first();
    }

    private function obtenerDetallesOrden($idPedido)
    {
        return DB::table('pedidos_detalle as pd')
            ->join('unidad_pedido as up', 'pd.id_unidad_pedido', '=', 'up.id_unidad_pedido')
            ->select(
                "pd.id_pdetalle", 
                "pd.nombre_producto as producto",
                "pd.plus_producto as plus", 
                "up.descripcion as unidadMedida",
                DB::raw("CONCAT(pd.cantidad, ' ', up.descripcion) as total"),
            )
            ->orderBy('pd.nombre_producto')
            ->where('pd.id_pedido', '=', $idPedido)
            ->get()
            ->map(function ($item) {
                return (array) $item;
            })
            ->toArray(); 
    }

    /* Reporte desde Informes (mas elaborado)*/
    public function generatePDF(Request $request)
    {
        $pedidos = $request->input('pedidos');
        $dataThead = $request->input('dataThead');
        $fecha = $request->input('fecha');
        $usuarioGenera = $request->user()->name;

        $numerosPedido = array_map(function ($numero) {
            return 'N° ' . $numero;
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


        $html = view('pdf.OrdenPDF', compact('fecha', 'nombreTiendas','numerosOrden', 'usuarioGenera', 'dataThead', 'pedidos'))->render();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream();
    }

}
