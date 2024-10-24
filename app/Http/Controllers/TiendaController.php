<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tienda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TiendaController extends Controller
{
    /* INDEX */
    public function index() {
        $tiendas = DB::table('tienda')
            ->select('id_tienda', 'nombre', 'codigo', 'direccion', 'telefono', 'estado')
            ->get();

        return Inertia::render('Admin/Stores/Stores', [
            'tiendas' => $tiendas
        ]);
    }

    /* NUEVA TIENDA */
    public function create() {
        return Inertia::render('Admin/Stores/RegisterStore');
    }

    public function store(Request $request) {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:100',
            'codigo' => 'required|string|max:20',
            'direccion' => 'required|string|max:255',
            'telefono' => 'required|string|max:30'
        ]);

        DB::beginTransaction();
        $tienda = new Tienda();
        $tienda->nombre = $validatedData['nombre'];
        $tienda->codigo = $validatedData['codigo'];
        $tienda->direccion = $validatedData['direccion'];
        $tienda->telefono = $validatedData['telefono'];
        $tienda->estado = 'Activo';
        $tienda->usuario_crea = $request->user()->id;
        if (!$tienda->save()) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error al guardar la tienda.']);
        }

        DB::commit();
        return redirect()->route('stores')->with('success', 'Tienda creada exitosamente');
    }
}
