<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function create(Request $request) {
        $buscador = $request->input('dates');
        //dd($buscador);
        $userId = $request->user()->id;
        $rol = $request->user()->id_rol;
        $showTiendasThead = [];

        $showTiendas = $rol == 1 ? $this->showTiendasAdmin() : $this->showTiendasEncargado($userId);
        //$pedidos = empty($buscador) ? $this->showPedidosIndex($rol, $userId) : $this->showPedidosFilter($rol, $userId, $buscador);
        //dd($pedidos);

        if (!empty($buscador)) {
            $showTiendasThead = $this->showTiendasThead($showTiendas, $buscador['id_tienda']);
            $showDataTBody = $this->showDataTbody($showTiendas, $buscador);
            dd($showDataTBody);
        }
        
        $showTiendas->prepend((object) [
            'id_tienda' => 0,
            'nombre_tienda' => 'Todas las tiendas'
        ]);

        return Inertia::render('Reports/Reports', [
            'pedidos' => [],
            'tiendas' => $showTiendas,
            'dataThead' => $showTiendasThead,
        ]);
    }

    private function showTiendasAdmin() {
        return DB::table('tienda')
            ->select('id_tienda', 'nombre as nombre_tienda', 'codigo')
            ->where('estado', 'Activo')
            ->orderBy('nombre')
            ->get();
    }

    private function showTiendasEncargado($id) {
        return DB::table('accesos as a')
            ->join('tienda as t', 't.id_tienda', '=', 'a.id_tienda')
            ->select('t.id_tienda', 't.nombre as nombre_tienda', 't.codigo')
            ->where('a.id_user', $id)
            ->get();
    }

    private function showTiendasThead($tiendas, $id_tienda){
        // Retornar todas las tiendas
        if ($id_tienda == 0) {
            return $tiendas;
        }

        // Retornar la tienda especifica
        foreach ($tiendas as $tienda) {
            if ($tienda->id_tienda == $id_tienda) {
                return [$tienda]; // Retornar como array con un solo registro
            }
        }

        return [];
    }

    private function showDataTbody($tiendas, $filtro){
        $existenPedidos = $this->verificarExistencia($tiendas, $filtro);
        if (empty($existenPedidos)) {
            return [];
        }

        $productos = $this->getProducts($existenPedidos);
        //return $existeProducto;
    }

    private function verificarExistencia($tiendas, $filtro) {
        $existeProducto = [];

        if ($filtro['id_tienda'] == 0) {
            foreach($tiendas as $tienda) {
                $data = DB::table('pedidos')
                    ->select('id_pedido', 'fecha_pedido', 'numero_pedido', 'id_tienda')
                    ->where('fecha_pedido', $filtro['fecha'])
                    ->where('id_tienda', $tienda->id_tienda)
                    ->get();

                if (!$data->isEmpty()) {
                    $existeProducto = array_merge($existeProducto, $data->toArray());
                }
            }
        } else {
            $data = DB::table('pedidos')
                ->select('id_pedido', 'fecha_pedido', 'numero_pedido', 'id_tienda')
                ->where('fecha_pedido', $filtro['fecha'])
                ->where('id_tienda', $filtro['id_tienda'])
                ->get();
            
            if (!$data->isEmpty()) {
                $existeProducto = array_merge($existeProducto, $data->toArray());
            }
        }

        return $existeProducto;
    }

    private function getProducts($pedidos) {
        $productos = [];
        foreach ($pedidos as $pedido) {
            $data = DB::table('pedidos_detalle as pd')
                ->join('unidad_pedido as u', 'u.id_unidad_pedido', '=', 'pd.id_unidad_pedido')
                ->select('pd.plus_producto', 'pd.nombre_producto', DB::raw("CONCAT(pd.cantidad,' ', u.codigo) as cantidad"), 'pd.id_producto', 'u.id_unidad_pedido')
                ->where('pd.id_pedido', $pedido['id_pedido']);
                
            if (!$data->isEmpty()) {
                $productos = array_merge($productos, $data->toArray());
            }
        }
        
        dd($productos);
    }
}
