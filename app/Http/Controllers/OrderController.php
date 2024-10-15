<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Access;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
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
    public function create() {
        return Inertia::render('Orders/InsertOrder');
    }
}
