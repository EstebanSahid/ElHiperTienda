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
        ], [
            'descripcion.required' => 'La descripcion es requerida',
            'codigo.required' => 'El codigo es requerido'
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

    /* EDITAR UNIDAD */
    public function renderEdit($id) {
        $unidad = UnidadMedida::find($id);
        
        return Inertia::render('Admin/Unidad/EditUnidad', [
            'unidad' => $unidad
        ]);
    }

    public function update(Request $request) {
        $validatedData = $request->validate([
            'descripcion' => 'required|string',
            'codigo' => 'required|string',
            'estado' => 'required|string',
            'id_unidad_pedido' => 'required'
        ], [
            'descripcion.required' => 'La descripcion es requerida',
            'codigo.required' => 'El codigo es requerido'
        ]);

        DB::beginTransaction();

        $unidadMedida = UnidadMedida::find($validatedData['id_unidad_pedido']);
        $unidadMedida->descripcion = $validatedData['descripcion'];
        $unidadMedida->codigo = $validatedData['codigo'];
        if ($validatedData['estado'] == 'Inactivo') {
            $unidadMedida->estado = 'Activo';
        }

        if (!$unidadMedida->save()) {
            DB::rollBack();
            return redirect()->back()->withErrors(['errors' => 'Error al Actualizar la Unidad de medida']);
        }

        DB::commit();
        return redirect()->route('unidad.medida')->with('success', 'Unidad de medida Actualizada exitosamente');
    }

    /* DESACTIVAR UNIDAD */
    public function deactivate(Request $request) {
        $validatedData = $request->validate([
            'id_unidad_pedido' => 'required'
        ]);

        DB::beginTransaction();
        $unidadMedida = UnidadMedida::find($validatedData['id_unidad_pedido']);
        $unidadMedida->estado = 'Inactivo';

        if (!$unidadMedida->save()) {
            DB::rollBack();
            return redirect()->back()->withErrors(['errors' => 'Error al Actualizar la Unidad de medida']);
        }

        DB::commit();
        return redirect()->route('unidad.medida')->with('success', 'Unidad de medida Actualizada exitosamente');
    }
}
