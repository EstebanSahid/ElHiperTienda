<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Access;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Mockery\Undefined;

class OrderController extends Controller
{
    /* LISTAR TIENDAS PARA GENERAR ORDENES */
    public function index(Request $request) {
        $userId = $request->user()->id;
        $rol = $request->user()->id_rol;

        if ($rol == 1) {
            $showTiendas = $this->indexAdmin();
        } else {
            $showTiendas = $this->indexEncargado($userId);
        }
        $showTiendas = $this->verificarExistencia($showTiendas);
        //$showTiendas = $this->validacionMostrar($showTiendas);
        //dd($showTiendas);
        return Inertia::render('Dashboard', [
            'tiendas' => $showTiendas,
        ]);
    }

    private function indexAdmin() {
        return DB::table('tienda')
            ->select('id_tienda', 'nombre', 'codigo')
            ->where('estado', 'Activo')
            ->orderBy('nombre')
            ->paginate(5);
    }

    private function indexEncargado($userId) {
        return DB::table('tienda as t')
            ->join('accesos as a', 'a.id_tienda', '=', 't.id_tienda')
            ->select('t.id_tienda', 't.nombre', 't.codigo')
            ->where('a.id_user', $userId)
            ->orderBy('t.nombre')
            ->paginate(5);
    }

    private function validacionMostrar($tiendas) {
        $horaInicio = 22;
        $horaFin = 0;
        $horaActual = date('H');
        if ($horaActual >= $horaInicio || $horaActual == $horaFin) {
            return $this->Procesados($tiendas);
        } else {
            return $this->verificarExistencia($tiendas);
        }
    }

    private function Procesados($tiendas) {
        foreach ($tiendas->items() as $tienda) {
            $tienda->procesado = 1;
        }
        return $tiendas;
    }

    private function verificarExistencia($tiendas) {
        $hoy = date('Y-m-d');

        // Recorremos los datos de una pagina
        foreach ($tiendas->items() as $tienda) {
            $pedido = DB::selectOne("
                SELECT id_pedido 
                FROM pedidos 
                WHERE id_tienda = :id_tienda 
                AND fecha_pedido = :fecha",
                [
                    'id_tienda' => $tienda->id_tienda,
                    'fecha' => $hoy
                ]
            );
    
            $tienda->procesado = $pedido ? 1 : 0;
        }
    
        return $tiendas;
    }

    /* GENERAR ORDEN */
    public function create(Request $request, $id) {
        $buscador = $request->input('search');

        $productos = $this->getProducts($buscador);
        $unidadPedido = $this->getUnidad();
        $tienda = $this->getTiendas($id);

        return Inertia::render('Orders/InsertOrder', [
            'productos' => $productos,
            'filtro' => $request->all('search'),
            'unidadMedida' => $unidadPedido,
            'tienda' => $tienda
        ]);
    }

    public function store(Request $request) {
        $validatedData = $request->validate([
            'fecha' => ['required', 'date_format:Y-m-d'],
            'idTienda' => ['required', 'integer'],
            'pedido' => ['required', 'array', 'min:1'],
            'pedido.*.plus' => ['required', 'string'],
            'pedido.*.nombre' => ['required', 'string'],
            'pedido.*.id_producto' => ['required', 'integer'],
            'pedido.*.id_unidad' => ['required', 'integer'],
            'pedido.*.cantidad' => ['required', 'numeric'],
        ]);

        $userCreate = $request->user()->id;
        $ultimoNumeroPedido = Order::max('numero_pedido');
        $nuevoNumeroPedido = $ultimoNumeroPedido ? $ultimoNumeroPedido + 1 : 1;

        // Creamos la orden
        DB::beginTransaction();

        $orden = new Order();
        $orden->fecha_pedido = $validatedData['fecha'];
        $orden->numero_pedido = $nuevoNumeroPedido; 
        $orden->estado = 'Activo';
        $orden->id_tienda = $validatedData['idTienda'];
        $orden->ucrea = $userCreate;
        $orden->save();

        // Obtenemos el ID para el detalle del pedido
        $ordenId = $orden->id_pedido;
        
        // Enviamos en lotes mas pequeños
        collect($validatedData['pedido'])->chunk(30)->each(function ($productosLote) use ($ordenId, $userCreate){
            foreach($productosLote as $producto) {
                $ordenDetalle = new OrderDetails();
                $ordenDetalle->nombre_producto = $producto['nombre'];
                $ordenDetalle->plus_producto = $producto['plus'];
                $ordenDetalle->cantidad = $producto['cantidad'];
                $ordenDetalle->estado = 'Activo';
                $ordenDetalle->id_producto = $producto['id_producto'];
                $ordenDetalle->id_pedido = $ordenId;
                $ordenDetalle->id_unidad_pedido = $producto['id_unidad'];
                $ordenDetalle->ucrea = $userCreate;
                if (!$ordenDetalle->save()) {
                    DB::rollBack();
                    return redirect()->back()->withErrors(['error' => 'Error al guardar los productos a la orden.']);
                }
            }
        });

        DB::commit();

        return redirect()->route('dashboard')->with('success', 'Orden creada exitosamente');
    }

    /* EDITAR ORDEN */
    public function renderEdit(Request $request, $id) {
        $buscador = $request->input('search');

        // Construimos la consulta con el QueryBuilder
        $productos = $this->getProducts($buscador);
        $unidadPedido = $this->getUnidad();
        $tienda = $this->getTiendas($id);
        $productosRegistrados = $this->getProductsOrder($id);

        return Inertia::render('Orders/EditOrder', [
            'productos' => $productos,
            'filtro' => $request->all('search'),
            'unidadMedida' => $unidadPedido,
            'tienda' => $tienda,
            'productosOrden' => $productosRegistrados,
            'productosOrdenEditar' => $productosRegistrados
        ]);
    }

    public function update(Request $request) {
        $ordenId = null;
        $userUpdate = $request->user()->id;
        foreach ($request->pedido as $prod) {
            $ordenId = $prod['id_pedido'];
            break;
        }
        
        // Enviamos en lotes mas pequeños
        collect($request->pedido)->chunk(30)->each(function ($productosLote) use ($ordenId, $userUpdate){
            foreach($productosLote as $producto) {
                if (!isset($producto['id_pdetalle'])) {
                    // Si no esta definido un pdetalle contará como nuevo registro
                } else {
                    // Si pdetalle está definido contrará como actualizacion
                }
                /*
                $ordenDetalle = new OrderDetails();
                $ordenDetalle->nombre_producto = $producto['nombre'];
                $ordenDetalle->plus_producto = $producto['plus'];
                $ordenDetalle->cantidad = $producto['cantidad'];
                $ordenDetalle->estado = 'Activo';
                $ordenDetalle->id_producto = $producto['id_producto'];
                $ordenDetalle->id_pedido = $ordenId;
                $ordenDetalle->id_unidad_pedido = $producto['id_unidad'];
                $ordenDetalle->ucrea = $userUpdate;
                if (!$ordenDetalle->save()) {
                    DB::rollBack();
                    return redirect()->back()->withErrors(['error' => 'Error al guardar los productos a la orden.']);
                }
                */
            }
        });
    }

    private function storeNewProucts($products, $idPedido) {

    }

    private function updateProducts($products) {

    }

    public function getProductsOrder($id) {
        $dateHoy = Date('Y-m-d');
        return DB::table('pedidos_detalle as pd')
            ->join('unidad_pedido as up', 'pd.id_unidad_pedido', '=', 'up.id_unidad_pedido')
            ->join('pedidos as p', 'p.id_pedido', '=', 'pd.id_pedido')
            ->select('p.id_pedido','pd.id_pdetalle', 'pd.id_producto', 'pd.nombre_producto as nombre', 'pd.plus_producto as plus', 'pd.cantidad', 'up.codigo', 'up.id_unidad_pedido as id_unidad'/*, DB::raw('1 as existe')*/)
            ->where('p.id_tienda', '=', $id)
            ->where('p.fecha_pedido', '=', $dateHoy)
            ->orderBy('pd.nombre_producto')
            ->get();
    }

    /* FUNCIONES COMPARTIDAS */
    private function getProducts($buscador) {
        return DB::table('productos')
            ->select('plus', 'nombre', 'id_producto')
            ->where('nombre', 'LIKE', '%' . $buscador . '%')
            ->orWhere('plus', 'LIKE', '%' . $buscador . '%')
            ->orderBy('nombre')
            ->paginate(6)
            ->withQueryString()
            ->through(fn ($producto) => [
                'plus' => $producto->plus,
                'nombre' => $producto->nombre,
                'id_producto' => $producto->id_producto,
            ]);
    }

    private function getUnidad() {
        return DB::table('unidad_pedido')
            ->select('id_unidad_pedido', 'codigo')
            ->get();
    }

    private function getTiendas($id) {
        return DB::table('tienda')
            ->select('nombre', 'id_tienda')
            ->where('id_tienda', $id)
            ->get();
    }
}

