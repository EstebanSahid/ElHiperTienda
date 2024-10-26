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
            ->paginate(20);

            //dd($productos);
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

    /* EDITAR UNIDAD */
    public function renderEdit($id) {
        $producto = Producto::find($id);

        return Inertia::render('Admin/Products/EditProduct', [
            'producto' => $producto
        ]);
    }

    public function update(Request $request) {
        $validatedData = $request->validate([
            'plus' => 'required|string',
            'nombre' => 'required|string',
            'estado' => 'required|string',
            'id_producto' => 'required'
        ]);

        DB::beginTransaction();

        $producto = producto::find($validatedData['id_producto']);
        //dd($producto);        
        $producto->plus = $validatedData['plus'];
        $producto->nombre = $validatedData['nombre'];
        $producto->umodifica = $request->user()->id;
        if ($validatedData['estado'] == 'Inactivo') {
            $producto->estado = 'Activo';
        }

        if (!$producto->save()) {
            DB::rollBack();
            return redirect()->back()->withErrors(['errors' => 'Error al Actualizar el Producto']);
        }

        DB::commit();
        return redirect()->route('products')->with('success', 'Producto Actualizado exitosamente');
    }

    /* DESACTIVAR UNIDAD */
    public function deactivate(Request $request) {
        $validatedData = $request->validate([
            'id_producto' => 'required'
        ]);

        DB::beginTransaction();
        $producto = producto::find($validatedData['id_producto']);
        $producto->estado = 'Inactivo';

        if (!$producto->save()) {
            DB::rollBack();
            return redirect()->back()->withErrors(['errors' => 'Error al dar de baja el producto']);
        }

        DB::commit();
        return redirect()->route('products')->with('success', 'Producto Dado de baja exitosamente');
    }

}
