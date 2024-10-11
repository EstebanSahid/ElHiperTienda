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
    public function index(Request $request) {
        $userId = $request->user()->id;
        $rol = $request->user()->id_rol;

        if ($rol == 1) {
            $showTiendas = $this->indexAdmin($userId);
        } else {
            $showTiendas = $this->indexEncargado($userId);
        }

        //dd($showTiendas);
        return Inertia::render('Dashboard', [
            'tiendas' => $showTiendas,
        ]);
    }

    private function indexAdmin() {
        return DB::select("SELECT * FROM tienda WHERE estado = 'Activo'");
    }

    private function indexEncargado($userId) {
        return DB::select("
            SELECT t.id_tienda, t.nombre, t.codigo 
            FROM tienda t
                INNER JOIN accesos a ON t.id_tienda = a.id_tienda
            WHERE id_user = :id", 
            ['id' => $userId]
        );
    }
}
