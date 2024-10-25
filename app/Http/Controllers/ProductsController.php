<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ProductsController extends Controller
{
    public function index() {
        $productos = DB::table('productos')
            ->select('id_producto','plus', 'nombre', 'estado')
            ->orderBy('nombre')
            ->paginate(7);

        return Inertia::render('Admin/Products/Products', [
            'productos' => $productos
        ]);
    }
}
