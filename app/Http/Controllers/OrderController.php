<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Access;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;


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

        // Construimos la consulta con el QueryBuilder
        $productos = DB::table('productos')
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

        $unidadPedido = DB::table('unidad_pedido')
            ->select('id_unidad_pedido', 'codigo')
            ->get();

        $tienda = DB::table('tienda')
            ->select('nombre', 'id_tienda')
            ->where('id_tienda', $id)
            ->get();

        return Inertia::render('Orders/InsertOrder', [
            'productos' => $productos,
            'filtro' => $request->all('search'),
            'unidadMedida' => $unidadPedido,
            'tienda' => $tienda
        ]);
    }

    public function store(Request $request) {
        dd($request->all());
    }

    /*
    public function create1(Request $request) {
        $buscador = $request->input('params.buscador');
        //dd($buscador);
        if ($buscador == null){
            return Inertia::render('Orders/InsertOrder', [
                'productos' => []
            ]);
        } else {
            $productos = DB::table('productos')
                ->select('plus', 'nombre', 'id_producto')
                ->where('nombre', 'LIKE', '%'. $buscador .'%')
                ->orWhere('plus', 'LIKE', '%'. $buscador .'%')
                ->orderBy('nombre')
                ->paginate(6);
            
            return Inertia::render('Orders/InsertOrder', [
                'productos' => $productos
            ]);
        }
    }
    */

    /*
    public function traerProductos(Request $request) {
        $buscador = $request->input('params.buscador');

        $productos = DB::table('productos')
            ->select('plus', 'nombre', 'id_producto')
            ->where('nombre', 'LIKE', '%'. $buscador .'%')
            ->orWhere('plus', 'LIKE', '%'. $buscador .'%')
            ->orderBy('nombre')
            ->paginate(6);

            return $productos;
    }
    */
    
}
