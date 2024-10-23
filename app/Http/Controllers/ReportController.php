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
        $showDataTBody = [];

        $showTiendas = $rol == 1 ? $this->showTiendasAdmin() : $this->showTiendasEncargado($userId);

        if (!empty($buscador)) {
            $showTiendasThead = $this->showTiendasThead($showTiendas, $buscador['id_tienda']);
            $showDataTBody = $this->showDataTbody($showTiendas, $buscador);
        }

        //dd($showDataTBody);
        
        $showTiendas->prepend((object) [
            'id_tienda' => 0,
            'nombre_tienda' => 'Todas las tiendas'
        ]);

        return Inertia::render('Reports/Reports', [
            'tiendas' => $showTiendas,
            'dataThead' => $showTiendasThead,
            'pedidos' => $showDataTBody,
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
            ->orderBy('t.nombre')
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
        $organizarProductos = $this->organizarProductos($productos);
        return $organizarProductos;
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
                ->join('pedidos as p', 'p.id_pedido', '=', 'pd.id_pedido')
                ->select('p.id_tienda','pd.id_pedido', 'pd.plus_producto', 'pd.nombre_producto', DB::raw("CONCAT(pd.cantidad,' ', u.codigo) as cantidad_concat"), 'pd.id_producto', 'u.id_unidad_pedido', 'pd.cantidad', 'u.descripcion')
                ->where('pd.id_pedido', $pedido->id_pedido)
                ->get();

                
            if (!$data->isEmpty()) {
                $productos = array_merge($productos, $data->toArray());
            }
        }
        //dd($pedidos);
        return $productos;
    }

    private function organizarProductos($productos) {
        $rowData = [];
        $pedidos = [];
    
        // Organizar los productos y pedidos
        foreach ($productos as $producto) {
            $idProducto = $producto->id_producto;
            $idPedido = $producto->id_tienda;
            $cantidad = $producto->cantidad;
            $idUnidad = $producto->id_unidad_pedido;
            $cantidadConcat = $producto->cantidad_concat;
            $nombreUnidad = $producto->descripcion;
    
            // Si el id_producto no está en el array, lo inicializamos
            if (!isset($rowData[$idProducto])) {
                $rowData[$idProducto] = [
                    'plus_producto' => $producto->plus_producto,
                    'nombre_producto' => $producto->nombre_producto,
                    'pedidos' => [],
                    'nombresUnidad' => [],
                    'totales' => [],
                ];
            }
    
            // Guardamos la cantidad en la columna correspondiente al id_pedido
            $rowData[$idProducto]['pedidos'][$idPedido] = $cantidadConcat;
            $rowData[$idProducto]['nombresUnidad'][$idUnidad] = $nombreUnidad;

            // Verificamos, si existe un total se suma
            if (isset($rowData[$idProducto]['totales'][$idUnidad])) {
                $rowData[$idProducto]['totales'][$idUnidad] += $cantidad;
            } else {
                // Si no existe, inicializamos con la cantidad actual
                $rowData[$idProducto]['totales'][$idUnidad] = $cantidad;
            }
    
            // Guardamos los id_pedido únicos para generar las columnas dinámicas
            if (!in_array($idPedido, $pedidos)) {
                $pedidos[] = $idPedido;
            }
        }
        //dd($rowData);
    
        // Generamos y organizamos la tabla con los productos clasificados por producto
        $output = [];
        
        foreach ($rowData as $idProducto => $productoData) {
            $total = [];

            foreach ($productoData['nombresUnidad'] as $key => $unidad) {
                if (isset($productoData['totales'][$key])) {
                    $cantidad = $productoData['totales'][$key];
                    // Agregamos la cantidad y la unidad al arreglo de totales
                    $totales[] = $cantidad . ' ' . $unidad;
                }
            }

            $total = implode(', ', $totales);
            $totales = [];

            $fila = [
                'plus' => $productoData['plus_producto'],
                'producto' => $productoData['nombre_producto'],
                'total' => $total
            ];
    
            // Para cada id_pedido, agregamos la cantidad o un guion si no existe
            foreach ($pedidos as $idPedido) {
                $fila['pedido_' . $idPedido] = $productoData['pedidos'][$idPedido] ?? '-';
            }
        
            $output[] = $fila;
        }
        //dd($output);
        return $output;
    }
}