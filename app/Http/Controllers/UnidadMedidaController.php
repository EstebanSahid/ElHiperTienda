<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class UnidadMedidaController extends Controller
{
    /* INDEX UNIDAD DE MEDIDA */
    public function index() {
        $unidades = DB::table('unidad_pedido')
            ->select('id_unidad_pedido', 'descripcion', 'codigo', 'estado')
            ->orderBy('descripcion')
            ->paginate(7);
        return Inertia::render('Admin/Unidad/Unidad', [
            'unidades' => $unidades
        ]);
    }

    /* NUEVA UNIDAD DE MEDIDA */
    public function create() {
        return Inertia::render('Admin/Unidad/RegisterUnidad');
    }

    public function store(Request $request) {
        $validatedData = $request->validate([
            'descripcion' => 'required|string',
            'codigo' => 'required|string'
        ]);

        DB::beginTransaction();
        $unidadMedida = new UnidadMedida();
        $unidadMedida->descripcion = $validatedData['descripcion'];
        $unidadMedida->codigo = $validatedData['codigo'];
        $unidadMedida->estado = 'Activo';
        if (!$unidadMedida->save()) {
            DB::rollBack();
            return redirect()->back()->withErrors(['errors' => 'Error al guardar la Unidad de medida']);
        }

        DB::commit();
        return redirect()->route('unidad.medida')->with('success', 'Unidad de medida creado exitosamente');

    }
}
