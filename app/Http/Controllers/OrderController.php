<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PermisosController;
use App\Enums\RolEnum;
use App\Models\Order;
use App\Models\OrderDetails;
use app\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Carbon\Carbon;

class OrderController extends Controller
{
    /* LISTAR TIENDAS PARA GENERAR ORDENES */
    public function index(Request $request) {
        try{
            $showTiendas = $this->obtenerTiendasPorRol($request->user(), true);
            $showTiendas = $this->verificarExistencia($showTiendas);
    
            return Inertia::render('Dashboard', [
                'tiendas' => $showTiendas,
            ]);

        } catch (\Exception $e) {
            Log::error('Error al cargar el dashboard: '. $e->getMessage());

            return Inertia::render('Dashboard', [
                'tiendas' => [],
                'error' => [
                    'mensaje' => 'Error al cargar el dashboard: '. $e->getMessage(),
                    'duracionNotificacion' => 10
                ]
            ], 500);

        }
        
    }

    private function obtenerTiendasPorRol(User $user, bool $paginado = true) {
        return match(RolEnum::from($user->id_rol)) {
            RolEnum::ADMINISTRADOR  => $this->tiendasAdmin($paginado),
            RolEnum::USUARIO        => $this->tiendasUsuario($user->id, $paginado),
            default                 => throw new \Exception('Rol no permitido para acceder a las tiendas')
        };
    }

    private function tiendasAdmin(bool $paginado) {
        $tiendas = DB::table('tienda')
            ->select('id_tienda', 'nombre', 'codigo')
            ->where('estado', 'Activo')
            ->orderBy('nombre');
            
        return $paginado ? $tiendas->paginate(5) : $tiendas->get();
    }

    private function tiendasUsuario($userId, bool $paginado) {
        $tiendas = DB::table('tienda as t')
            ->join('accesos as a', 'a.id_tienda', '=', 't.id_tienda')
            ->select('t.id_tienda', 't.nombre', 't.codigo')
            ->where('a.id_user', $userId)
            ->where('t.estado', 'Activo')
            ->orderBy('t.nombre');

        return $paginado ? $tiendas->paginate(5) : $tiendas->get();
    }

    private function validacionMostrar($tiendas) {
        // try{
        //     $horaInicio = 22;
        //     $horaFin = 0;
        //     $horaActual = (int) date('H');

            // Si el rango pasa la media noche 
            // if ($horaActual >= $horaInicio || $horaActual == $horaFin) {
            //     return $this->Procesados($tiendas);
            // } else {
            //     return $this->verificarExistencia($tiendas);
            // }

        // }catch(\Exception $e){
        //     Log::error('Error al mostrar las tiendas por hora:' . $e->getMessage());
        //     throw new \Exception('Error al mostrar las tiendas por hora: ' . $e->getMessage());
        // }
    }

    private function Procesados($tiendas) {
        foreach ($tiendas->items() as $tienda) {
            $tienda->procesado = 1;
        }
        return $tiendas;
    }

    private function verificarExistencia($tiendas) {
        try {
            $timezone = config('app.timezone'); 
            $hoy = Carbon::now($timezone)->format('Y-m-d');

            // Obtenemos los ids de las tiendas
            $idsTiendas = collect($tiendas->items())->pluck('id_tienda');

            // Obtenemos los pedidos registrados hoy por tienda
            $pedidosRegistrados = DB::table('pedidos')
                ->select('id_tienda', 'id_pedido')
                ->whereIn('id_tienda', $idsTiendas)
                ->whereDate('fecha_pedido', $hoy)
                ->get()
                ->keyBy('id_tienda'); // indexamos por la tienda

            // Marcamos las tiendas segun si hay pedidos o no
            foreach ($tiendas->items() as $tienda) {
                $pedido = $pedidosRegistrados->get($tienda->id_tienda);
                $tienda->procesado = $pedido ? 1 : 0;
                $tienda->id_pedido = $pedido ? $pedido->id_pedido : null;
            }

            return $tiendas;

        } catch(\Exception $e) {
            Log::error('Error al generar existencia de pedidos: ' . $e->getMessage());
            throw new \Exception('Error al generar existencia de pedidos: ' . $e->getMessage());
        }
    }

    /* GENERAR ORDEN */
    public function create(Request $request, $id) {
        try {
            $buscador = $request->input('search');
    
            $productos = $this->getProducts($buscador);
            $unidadPedido = $this->getUnidad();
            $tienda = $this->obtenerTiendaPorId($id);
    
            return Inertia::render('Orders/InsertOrder', [
                'productos' => $productos,
                'filtro' => $request->all('search'),
                'unidadMedida' => $unidadPedido,
                'tienda' => $tienda
            ]);
        }catch (\Exception $e) {
            Log::error('Error al cargar la vista de crear orden: ' . $e->getMessage());

            return Inertia::render('Orders/InsertOrder', [
                'productos' => [],
                'filtro' => $request->all('search'),
                'unidadMedida' => $this->getUnidad(),
                'tienda' => $this->obtenerTiendaPorId($id),
                'error' => [
                    'mensaje' => 'Error al cargar la vista de crear orden: ' . $e->getMessage(),
                    'duracionNotificacion' => 10
                ]
            ]);
        }
    }

    public function store(Request $request) {
        try{
            $validatedData = $request->validate([
                'fecha' => ['required', 'date_format:Y-m-d'],
                'idTienda' => ['required', 'integer'],
                'pedido' => ['required', 'array', 'min:1'],
                'pedido.*.plus' => ['required'],
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
                        return redirect()->back()->withInput()->with('error', 'Error al guardar los productos a la orden');
                    }
                }
            });

            DB::commit();

            return redirect()->route('dashboard')->with('success', 'Orden creada exitosamente');
        }catch(\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar la orden:' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error al guardar la orden:' . $e->getMessage());
        }
    }

    /* EDITAR ORDEN */
    public function renderEdit(Request $request, $id) {
        try{
            $buscador = $request->input('search');

            // Construimos la consulta con el QueryBuilder
            $productos = $this->getProducts($buscador);
            $unidadPedido = $this->getUnidad();
            $tienda = $this->obtenerTiendaPorId($id);
            $productosRegistrados = $this->getProductsOrder($id);
            $idPedido = $this->getPedido($id);

            return Inertia::render('Orders/EditOrder', [
                'productos' => $productos,
                'filtro' => $request->all('search'),
                'unidadMedida' => $unidadPedido,
                'tienda' => $tienda,
                'productosOrden' => $productosRegistrados,
                'productosOriginal' => $productosRegistrados,
                'id_pedido' => $idPedido
            ]);
        } catch (\Exception $e) {
            Log::error('Error al cargar la vista de editar orden: ' . $e->getMessage());
            return Inertia::render('Orders/EditOrder', [
                'productos' => [],
                'filtro' => $request->all('search'),
                'unidadMedida' => $this->getUnidad(),
                'tienda' => $this->obtenerTiendaPorId($id),
                'error' => [
                    'mensaje' => 'Error al cargar la vista de crear orden: ' . $e->getMessage(),
                    'duracionNotificacion' => 10
                ]
            ]);
        }
    }

    public function update(Request $request) {
        try{
            $ordenId = $request->idPedido;
            $userUpdate = $request->user()->id;
    
            DB::beginTransaction();
            // Verificamos la accion del front c = Create, u = update, d = delete
            foreach ($request->pedido as $prod) {
                $accion = $prod['accion'];
                switch ($accion) {
                    case 'c':
                        // Nuevo producto a la orden
                        $this->editStore($prod, $userUpdate, $ordenId);
                        break;
                    case 'u':
                        // Modificar producto a la orden
                        $this->editUpdate($prod, $userUpdate);
                        break;
                    case 'd':
                        // Eliminar un producto de la orden
                        $this->editDelete($prod);
                        break;
                    default:
                        
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error al actualizar la orden::' . $e->getMessage());
        }
        DB::commit();

        return redirect()->route('dashboard')->with('success', 'Orden Actualizada exitosamente');
    }

    private function editStore($product, $user, $idPedido) {
        try{
            $ordenDetalle = new OrderDetails();
            $ordenDetalle->nombre_producto = $product['nombre'];
            $ordenDetalle->plus_producto = $product['plus'];
            $ordenDetalle->cantidad = $product['cantidad'];
            $ordenDetalle->estado = 'Activo';
            $ordenDetalle->id_producto = $product['id_producto'];
            $ordenDetalle->id_pedido = $idPedido;
            $ordenDetalle->id_unidad_pedido = $product['id_unidad'];
            $ordenDetalle->ucrea = $user;
            $ordenDetalle->umodifica = $user;
            if (!$ordenDetalle->save()) {
                throw new \Exception('Error al guardar el producto');
            }
        } catch (\Exception $e) {
            Log::error('Error al agregar un producto a la orden ' . $product['nombre'] . $e->getMessage());
            throw $e; 
        }
    }

    private function editUpdate($product, $user) {
        try{
            $ordenDetalle = $this->verificarExistenciaProductoDetalle($product['id_pdetalle']);
    
            $ordenDetalle->id_unidad_pedido = $product['id_unidad'];
            $ordenDetalle->cantidad = $product['cantidad'];
            $ordenDetalle->umodifica = $user;
            if (!$ordenDetalle->save()) {
                throw new \Exception('Error al editar un producto de la orden ' . $product['nombre'] . ' ID: ' . $product['id_pdetalle']);
            }

        }catch (\Exception $e) {
            Log::error('Error al editar un producto de la orden ' . $product['nombre'] . ' ID: ' . $product['id_pdetalle'] . ' ' . $e->getMessage());
            throw $e;
        }
    }

    private function editDelete($product) {
        try{
            $ordenDetalle = $this->verificarExistenciaProductoDetalle($product['id_pdetalle']);

            if (!$ordenDetalle->delete()) {
                throw new \Exception('Error al eliminar el producto.');
            }
        } catch (\Exception $e) {
            Log::error('Error al eliminar un producto de la orden ' . $product['nombre'] . ' ID: ' . $product['id_pdetalle'] . ' ' . $e->getMessage());
            throw $e;
        }
    }

    private function verificarExistenciaProductoDetalle($id_pdetalle) {
        $ordenDetalle = OrderDetails::find($id_pdetalle);

        if (!$ordenDetalle) {
            $mensajeError = 'El producto no existe en la orden con ID detalle: ' . $id_pdetalle;
            Log::error($mensajeError);
            throw new \Exception($mensajeError);
        }

        return $ordenDetalle;
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

    public function getProductsOrderDuplicated($idPedido) {
        $dateHoy = Date('Y-m-d');
        return DB::table('pedidos_detalle as pd')
            ->join('unidad_pedido as up', 'pd.id_unidad_pedido', '=', 'up.id_unidad_pedido')
            ->join('pedidos as p', 'p.id_pedido', '=', 'pd.id_pedido')
            ->select('p.id_pedido','pd.id_pdetalle', 'pd.id_producto', 'pd.nombre_producto as nombre', 'pd.plus_producto as plus', 'pd.cantidad', 'up.codigo', 'up.id_unidad_pedido as id_unidad'/*, DB::raw('1 as existe')*/)
            ->where('p.id_pedido', '=', $idPedido)
            ->orderBy('pd.nombre_producto')
            ->get();
    }

    /* LISTAR ORDENES REGISTRADAS */

    public function indexOrders(Request $request) {
        try{
            // dd($request->input('dates'));
            $filtro = $request->input('dates', []);

            $tiendas = $this->obtenerTiendasPorRol($request->user(), false);
            $ordenes = $this->getOrders($tiendas, $request->user(), $filtro);

            $tiendas->prepend((object) [
                'id_tienda' => 0,
                'nombre' => 'Todas las tiendas'
            ]);
    
            return Inertia::render('Orders/HistoryOrders', [
                'ordenes' => $ordenes,
                'tiendas' => $tiendas
            ]);

        } catch (\Exception $e) {
            Log::error('Error al cargar las ordenes: '. $e->getMessage());

            return Inertia::render('Orders/HistoryOrders', [
                'ordenes' => [],
                'error' => [
                    'mensaje' => 'Error al cargar las ordenes: '. $e->getMessage(),
                    'duracionNotificacion' => 10
                ]
            ], 500);

        }
    }

    private function getOrders($tiendas, $user, $filtro) {
        $pedidos = DB::table('pedidos as p')
        ->join('tienda as t', 'p.id_tienda', '=', 't.id_tienda')
        ->select(
            'p.id_pedido', 
            DB::raw("CONCAT('N° ', LPAD(p.numero_pedido, 5, '0')) as numero_pedido"),
            'p.fecha_pedido', 
            't.nombre as nombre_tienda');
        
        if (!PermisosController::esAdministrador($user)) {
            $idsTiendas = $tiendas->pluck('id_tienda')->toArray();
            $pedidos->whereIn('p.id_tienda', $idsTiendas);
        } 

        $pedidos->where('p.estado', 'Activo');

        if (!empty($filtro['id_tienda']) && $filtro['id_tienda'] != 0) {
            $pedidos->where('p.id_tienda', '=', $filtro['id_tienda']);
        }

        $pedidos->orderBy('p.numero_pedido', 'desc');

        return $pedidos->paginate(5);
    }

    public function viewOrder(Request $request, $idPedido) {
        // dd($idPedido); 
        try {
            $productos = DB::table('pedidos_detalle as pd')
            ->join('unidad_pedido as up', 'pd.id_unidad_pedido', '=', 'up.id_unidad_pedido')
            ->select(
                "pd.id_pdetalle", 
                "pd.nombre_producto",
                "pd.plus_producto", 
                "pd.cantidad", 
                "up.descripcion as unidadMedida"
            )
            ->orderBy('pd.nombre_producto')
            ->where('pd.id_pedido', '=', $idPedido)
            ->get();

            $pedido = DB::table('pedidos as p')
                ->join('tienda as t', 'p.id_tienda', '=', 't.id_tienda')
                ->select('p.id_pedido', 'p.fecha_pedido as fecha', 'p.id_tienda', 't.nombre as tienda')
                ->where('p.id_pedido', '=', $idPedido)
                ->first();

            return Inertia::render('Orders/ViewOrder', [
                'productos' => $productos,
                'pedido' => $pedido,
            ]);
        }catch (\Exception $e) {
            Log::error('Error al cargar el pedido: '. $e->getMessage());

            return Inertia::render('Orders/ViewOrder', [
                'productos' => [],
                'pedido' => [],
                'error' => [
                    'mensaje' => 'Error al cargar el pedido: '. $e->getMessage(),
                    'duracionNotificacion' => 10
                ]
            ], 500);
        }
    }

    /*  DUPLICAR ORDEN */
    public function renderDuplicate(Request $request, $idPedido, $idTienda) {
        $buscador = $request->input('search');

        // Construimos la consulta con el QueryBuilder
        $productos = $this->getProducts($buscador);
        $unidadPedido = $this->getUnidad();
        $tienda = $this->obtenerTiendaPorId($idTienda);
        $productosRegistrados = $this->getProductsOrderDuplicated($idPedido);
        $idPedido = $this->getPedido($idTienda);

        return Inertia::render('Orders/DuplicateOrder', [
            'productos' => $productos,
            'filtro' => $request->all('search'),
            'unidadMedida' => $unidadPedido,
            'tienda' => $tienda,
            'productosOrden' => $productosRegistrados,
            'productosOriginal' => $productosRegistrados,
            'id_pedido' => $idPedido
        ]);
    }

    /* FUNCIONES COMPARTIDAS */
    private function getProducts($buscador) {
        try {
            return DB::table('productos')
                ->select('plus', 'nombre', 'id_producto')
                ->when($buscador, function ($query, $buscador) {
                    $query->where(function ($q) use ($buscador) {
                        $q->where('nombre', 'LIKE', '%' . $buscador . '%')
                        ->orWhere('plus', 'LIKE', '%' . $buscador . '%');
                    });
                })
                ->orderBy('nombre')
                ->paginate(6)
                ->withQueryString()
                ->through(fn ($producto) => [
                    'plus' => $producto->plus,
                    'nombre' => $producto->nombre,
                    'id_producto' => $producto->id_producto,
                ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener productos: ' . $e->getMessage());
            throw $e;
            // return collect(); // o return []; dependiendo de cómo lo manejes en Vue/Inertia
        }
    }

    private function getUnidad() {
        return DB::table('unidad_pedido')
            ->select('id_unidad_pedido', 'codigo')
            ->where('estado', 'Activo')
            ->get();
    }

    private function obtenerTiendaPorId($id) {
        return DB::table('tienda')
            ->select('nombre', 'id_tienda')
            ->where('id_tienda', $id)
            ->first();
    }

    private function getPedido($id) {
        $dateHoy = Date('Y-m-d');
        return DB::table('pedidos')
            ->select('id_pedido')
            ->where('id_tienda', $id)
            ->where('fecha_pedido', '=', $dateHoy)
            ->first();
    }
}

