<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class UnidadMedidaController extends Controller
{
    public function index() {
        $unidades = DB::table('unidad_pedido')
            ->select('id_unidad_pedido', 'descripcion', 'codigo', 'estado')
            ->orderBy('descripcion')
            ->paginate(7);
        return Inertia::render('Admin/Unidad/Unidad', [
            'unidades' => $unidades
        ]);
    }
}
