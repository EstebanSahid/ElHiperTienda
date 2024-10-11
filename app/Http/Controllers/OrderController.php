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
    // Mostrar para Insertar Pedidos
    public function index(Request $request) {
        $userId = $request->user()->id;
        $rol = $request->user()->id_rol;

        if ($rol == 1) {
            $showTiendas = $this->indexAdmin($userId);
        } else {
            $showTiendas = $this->indexEncargado($userId);
        }
        $showTiendas = $this->verificarExistencia($showTiendas);
        //$showTiendas = $this->validacionMostrar($showTiendas);

        // dd($showTiendas);
        return Inertia::render('Dashboard', [
            'tiendas' => $showTiendas,
        ]);
    }

    private function indexAdmin() {
        return DB::select("
        SELECT  
            id_tienda, nombre, codigo
        FROM tienda 
        WHERE estado = 'Activo'
        ORDER BY nombre");
    }

    private function indexEncargado($userId) {
        return DB::select("
            SELECT 
                t.id_tienda, t.nombre, t.codigo 
            FROM tienda t
                INNER JOIN accesos a ON t.id_tienda = a.id_tienda
            WHERE a.id_user = :id
                ORDER BY t.nombre", 
            ['id' => $userId]
        );
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
        foreach ($tiendas as $tienda) {
            $tienda->procesado = 1;
        }
        return $tiendas;
    }

    private function verificarExistencia($tiendas) {
        $hoy = date('Y-m-d');

        foreach ($tiendas as $tienda) {
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
    
            // Si encontramos un pedido, procesado será 1, de lo contrario 0
            $tienda->procesado = $pedido ? 1 : 0;
        }
    
        return $tiendas;
    }
}
