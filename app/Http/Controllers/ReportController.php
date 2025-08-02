<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;
use app\Enums\RolEnum;
use Illuminate\Support\Facades\Log;
use app\Models\Tienda;

class ReportController extends Controller
{
    public function create(Request $request) {
        try {
            $buscador = $request->input('dates');

            // Si no hay buscador o está vacío, inicializamos con la fecha actual y tienda cero para mostrar las del dia
            if (empty($buscador)) {
                $buscador = [
                    'fecha' => Carbon::now()->format('Y-m-d'),
                    'id_tienda' => 0
                ];
            }

            $userId = $request->user()->id;
            $rol = $request->user()->id_rol;

            $showTiendasThead = [];
            $showDataTBody = [];
            $numerosPedido = [];
            $showTiendasDuplicar = [];

            // Control para mostrar las Tiendas según sus accesos
            $showTiendas = $this->ShowTiendasSegunRol($rol, $userId);

            // Obtener la data del encabezado y el cuerpo cuando se uso algún filtro
            $showTiendasDuplicar = $this->tiendasDisponiblesParaDuplicar($showTiendas);
            $showTiendasThead = $this->showTiendasThead($showTiendas, $buscador['id_tienda']);

            $resultado = $this->showDataTbody($showTiendas, $buscador);
            $showDataTBody = $resultado['productosOrganizados'];
            $numerosPedido = $resultado['numerosPedido'];

            $showTiendas->prepend((object) [
                'id_tienda' => 0,
                'nombre_tienda' => 'Todas las tiendas'
            ]);

            return Inertia::render('Reports/Reports', [
                'tiendasDuplicar' => $showTiendasDuplicar,
                'tiendas' => $showTiendas,
                'dataThead' => $showTiendasThead,
                'pedidos' => $showDataTBody,
                'numerosPedido' => $numerosPedido
            ]);
        } catch (\Exception $e) {
            Log::error('Error al generar el reporte : ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Ocurrió un error al generar el reporte.');
        }
    }

    private function ShowTiendasSegunRol($rol, $userId) {
        $showTiendas = $rol === RolEnum::ADMINISTRADOR->value 
            ? $this->showTiendasAdmin() 
            : $this->showTiendasEncargado($userId);

        return $showTiendas;
    }

    private function showTiendasAdmin() {
        return DB::table('tienda')
            ->select('id_tienda', 'nombre as nombre_tienda', 'codigo')
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
        try {
            if ($id_tienda == 0) {
                return $tiendas;
            }
            
            $tienda = $tiendas->firstWhere('id_tienda', $id_tienda);
            return $tienda ? collect([$tienda]) : collect();

        } catch (\Exception $e) {
            Log::error('Error al generar la cabecera de tiendas: ' . $e->getMessage());
            throw $e;
        }
    }

    private function showDataTbody($tiendas, $filtro){
        // Verificar si hay productos en la orden
        try {
            $existenPedidos = $this->verificarExistencia($tiendas, $filtro);

            if (empty($existenPedidos)) {
                return [
                    'productosOrganizados' => [],
                    'numerosPedido' => []
                ];
            }
            
            $productos = $this->getProducts($existenPedidos);
            $organizarProductos = $this->organizarProductos($productos);

            // Extraer solo numero_pedido y formatear
            $numerosPedido = array_map(function ($pedido) {
                return str_pad($pedido->numero_pedido ?? '', 5, '0', STR_PAD_LEFT);
            }, $existenPedidos);

            return [
                'productosOrganizados' => $organizarProductos,
                'numerosPedido' => $numerosPedido
            ];
        } catch (\Exception $e) {
            Log::error("Error en showDataTbody: " . $e->getMessage());
            throw $e;    
        }
    }

    private function verificarExistencia($tiendas, $filtro) {
        try {
            if ($filtro['id_tienda'] == 0) {
                $idsTiendas = $tiendas->pluck('id_tienda')->toArray();
                
                $data = DB::table('pedidos')
                ->select('id_pedido', 'fecha_pedido', 'numero_pedido', 'id_tienda')
                ->where('fecha_pedido', $filtro['fecha'])
                ->whereIn('id_tienda', $idsTiendas)
                ->get();
                
                return $data->toArray();
            }
    
            // Caso contrario verificamos por tienda
            $data = DB::table('pedidos')
                ->select('id_pedido', 'fecha_pedido', 'numero_pedido', 'id_tienda')
                ->where('fecha_pedido', $filtro['fecha'])
                ->where('id_tienda', $filtro['id_tienda'])
                ->get();
    
            return $data->toArray();

        } catch (\Exception $e) {
            Log::error('Error al verificar la existencia de pedidos: ' . $e->getMessage());
            throw$e;
        }
        // Si la tienda es cero, significa que quiere ver todas las tiendas
    }

    private function getProducts($pedidos) {
        $pedidoIds = array_map(fn($pedido) => $pedido->id_pedido, $pedidos);

        $productos = DB::table('pedidos_detalle as pd')
        ->join('unidad_pedido as u', 'u.id_unidad_pedido', '=', 'pd.id_unidad_pedido')
        ->join('pedidos as p', 'p.id_pedido', '=', 'pd.id_pedido')
        ->select(
            'p.id_tienda',
            'pd.id_pedido',
            'pd.plus_producto',
            'pd.nombre_producto',
            DB::raw("CONCAT(pd.cantidad,' ', u.codigo) as cantidad_concat"),
            'pd.id_producto',
            'u.id_unidad_pedido',
            'pd.cantidad',
            'u.descripcion'
        )
        ->whereIn('pd.id_pedido', $pedidoIds)
        ->orderBy('pd.nombre_producto')
        ->get()
        ->toArray();

        return $productos;
    }

    private function organizarProductos($productos) {
        $rowData = [];
        $pedidos = [];
    
        // Organizar los productos y pedidos
        foreach ($productos as $producto) {
            $idProducto = $producto->id_producto;
            $idTienda = $producto->id_tienda;
            $cantidad = $producto->cantidad;
            $idUnidad = $producto->id_unidad_pedido;
            $cantidadConcat = $producto->cantidad_concat;
            $nombreUnidad = $producto->descripcion;
    
            // Si el id_producto no está en el array, lo inicializamos
            if (!isset($rowData[$idProducto])) {
                $rowData[$idProducto] = [
                    'plus_producto' => $producto->plus_producto,
                    'nombre_producto' => $producto->nombre_producto,
                    'id_pedido' => $producto->id_pedido,
                    'pedidos' => [],
                    'nombresUnidad' => [],
                    'totales' => [],
                ];
            }
    
            // Guardamos la cantidad en la columna correspondiente al id_pedido
            $rowData[$idProducto]['pedidos'][$idTienda] = $cantidadConcat;
            $rowData[$idProducto]['nombresUnidad'][$idUnidad] = $nombreUnidad;

            // Verificamos, si existe un total se suma
            if (isset($rowData[$idProducto]['totales'][$idUnidad])) {
                $rowData[$idProducto]['totales'][$idUnidad] += $cantidad;
            } else {
                // Si no existe, inicializamos con la cantidad actual
                $rowData[$idProducto]['totales'][$idUnidad] = $cantidad;
            }
    
            // Guardamos los id_pedido únicos para generar las columnas dinámicas
            if (!in_array($idTienda, $pedidos)) {
                $pedidos[] = $idTienda;
            }
        }
    
        return $this->generarDatosParaReporte($rowData, $pedidos);
    }

    private function generarDatosParaReporte($rowData, $pedidos) {
        // Generamos y organizamos la tabla con los productos clasificados por producto
        try {
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
                    'total' => $total,
                    'id_pedido' => $productoData['id_pedido']
                ];
        
                // Para cada Tienda, agregamos la cantidad o un guion si no existe
                foreach ($pedidos as $idTienda) {
                    $fila['pedido_' . $idTienda] = $productoData['pedidos'][$idTienda] ?? '-';
                }
            
                $output[] = $fila;
            }
    
            return $output;

        } catch (\Exception $e) {
            Log::error('Error al generar los datos para el reporte: ' . $e->getMessage());
            throw $e;
        }
    }

    private function tiendasDisponiblesParaDuplicar($tiendas) {
        try {
            $timezone = config('app.timezone'); 
            $hoy = Carbon::now($timezone)->format('Y-m-d');
            
            $tiendasConOrden = DB::table('pedidos') 
                ->where('fecha_pedido', '=', $hoy) 
                ->pluck('id_tienda') 
                ->toArray();
            
            $tiendasDisponibles = []; 
            foreach ($tiendas as $tienda) { 
                if (!in_array($tienda->id_tienda, $tiendasConOrden)) { 
                    $tiendasDisponibles[] = $tienda; 
                } 
            } 
            
            return $tiendasDisponibles;
        } catch (\Exception $e) {
            Log::error('Error al obtener tiendas disponibles para duplicar: ' . $e->getMessage());
            throw $e;
        }
    }
}
