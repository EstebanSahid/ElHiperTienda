<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ProductsController extends Controller
{
    /* INDEX PRODUCTO */
    public function index() {
        $productos = DB::table('productos')
            ->select('id_producto','plus', 'nombre', 'estado')
            ->orderBy('nombre')
            ->paginate(7);

        return Inertia::render('Admin/Products/Products', [
            'productos' => $productos
        ]);
    }

    /* NUEVO PRODUCTO */
    public function create() {
        return Inertia::render('Admin/Products/RegisterProduct');
    }

    public function store(Request $request) {
        $validatedData = $request->validate([
            'plus' => 'required|string',
            'nombre' => 'required|string'
        ]);

        DB::beginTransaction();
        $producto = new Producto();
        $producto->plus = $validatedData['plus'];
        $producto->nombre = $validatedData['nombre'];
        $producto->estado = 'Activo';
        $producto->ucrea = $request->user()->id;
        if (!$producto->save()) {
            DB::rollBack();
            return redirect()->back()->withErrors(['errors' => 'Error al guardar el producto']);
        }

        DB::commit();
        return redirect()->route('products')->with('success', 'Producto creado exitosamente');

    }

}
