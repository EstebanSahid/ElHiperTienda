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
    public function index(Request $request) {
        $buscador = $request->input('search.search', '');
        $orderBy = $request->input('search.orderBy', 'nombre');

        $productos = DB::table('productos')
            ->select('plus', 'nombre', 'id_producto', 'estado')
            ->where('nombre', 'LIKE', '%' . $buscador . '%')
            ->orWhere('plus', 'LIKE', '%' . $buscador . '%')
            ->orderBy($orderBy)
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($producto) => [
                'plus' => $producto->plus,
                'nombre' => $producto->nombre,
                'id_producto' => $producto->id_producto,
                'estado' => $producto->estado
            ]);

        return Inertia::render('Admin/Products/Products', [
            'productos' => $productos,
            'filtro' => [
                'search' => $request->input('search.search', ''),
                'orderBy' => $request->input('search.orderBy', 'nombre')
            ]
        ]);
    }

    /* NUEVO PRODUCTO */
    public function create() {
        return Inertia::render('Admin/Products/RegisterProduct');
    }

    public function store(Request $request) {
        $validatedData = $request->validate([
            'plus' => 'required|int|unique:productos,plus',
            'nombre' => 'required|string'
        ], [
            'plus.required' => 'El plus es obligatorio',
            'plus.int' => 'El Plus debe ser un número',
            'plus.unique' => 'Este plus ya está en uso, por favor utiliza otro',
            'nombre.required' => 'El nombre es obligatorio'
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

    /* EDITAR PRODUCTO */

    /* Antiguo
    public function renderEdit($id) {   
        $producto = Producto::find($id);

        return Inertia::render('Admin/Products/EditProduct', [
            'producto' => $producto
        ]);
    }

    public function update(Request $request) {
        $validatedData = $request->validate([
            'plus' => 'required|int',
            'nombre' => 'required|string',
            'estado' => 'required|string',
            'id_producto' => 'required'
        ], [
            'plus.required' => 'El plus es obligatorio',
            'plus.int' => 'El plus debe ser un numero',
            'nombre.required' => 'El nombre es requerido'
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
    */

    public function update(Request $request) {
        $validatedData = $request->validate([
            'id_producto' => 'required',
            'campo' => 'string|required|in:plus,nombre',
            'valor' => 'required'
        ]);

        try{
            DB::beginTransaction();

            $producto = producto::find($validatedData['id_producto']);
            if ($validatedData['campo'] == 'plus') {
                $producto->plus = $validatedData['valor'];
            } else if ($validatedData['campo'] == 'nombre') {
                $producto->nombre = $validatedData['valor'];
            }
            
            $producto->save();
            
            DB::commit();
        } catch(\Exception $e) {
            DB::rollBack();
        }
    }

    /* DESACTIVAR PRODUCTO */
    public function deactivate(Request $request) {
        $validatedData = $request->validate([
            'id_producto' => 'required'
        ]);

        DB::beginTransaction();
        $producto = producto::find($validatedData['id_producto']);
        $producto->estado = 'Inactivo';

        if (!$producto->save()) {
            DB::rollBack();
        }

        DB::commit();
    }

    /* ACTIVAR PRODUCTO */
    public function activate(Request $request) {
        $validatedData = $request->validate([
            'id_producto' => 'required'
        ]); 

        DB::beginTransaction();
        $producto = producto::find($validatedData['id_producto']);
        $producto->estado = 'Activo';

        if (!$producto->save()) {
            DB::rollBack();
        }

        DB::commit();
    }

    /* EDITAR PRODUCTOS MASIVOS */
    public function renderEdit() {
        $producto = Producto::all();
        //dd($producto);
        return Inertia::render('Admin/Configuration/Configuration', [
            'productos' => $producto
        ]);
    }
}
