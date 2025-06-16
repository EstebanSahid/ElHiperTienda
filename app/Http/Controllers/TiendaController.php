<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\OrdenEncabezado;
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
            ->orderBy('nombre')
            ->paginate(5);

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
        ], [
            'nombre.required' => 'El nombre de la tienda es requerido',
            'codigo.required' => 'El codigo es requerido',
            'direccion.required' => 'La direccion es requerida',
            'telefono.required' => 'El telefono es requerido'
        ]);

        DB::beginTransaction();

        try {
            // Crear nueva tienda
            $tienda = new Tienda();
            $tienda->nombre = $validatedData['nombre'];
            $tienda->codigo = $validatedData['codigo'];
            $tienda->direccion = $validatedData['direccion'];
            $tienda->telefono = $validatedData['telefono'];
            $tienda->estado = 'Activo';
            $tienda->usuario_crea = $request->user()->id;
            
            if (!$tienda->save()) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('error', 'Error al guardar la tienda.');
            }
        
            // Obtener el id de la tienda recién creada y la posición
            $idTienda = $tienda->id_tienda;
            $maxPosicion = DB::table('orden_encabezado')->max('posicion');
            $nuevaPosicion = $maxPosicion ? $maxPosicion + 1 : 1;
        
            // Crear nuevo registro en orden_encabezado
            $encabezado = new OrdenEncabezado();
            $encabezado->id_tienda = $idTienda;
            $encabezado->posicion = $nuevaPosicion;
            
            if (!$encabezado->save()) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('error', 'Error al guardar el encabezado de la orden.');
            }
        
            DB::commit();
            return redirect()->route('stores')->with('success', 'Tienda creada exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error al crear la tienda: ' . $e->getMessage());
        }        
    }

    /* EDITAR TIENDA */
    public function renderEdit($id) {
        $tienda = Tienda::find($id);

        return Inertia::render('Admin/Stores/EditStore', [
            'tienda' => $tienda
        ]);
    }

    public function update(Request $request) {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:100',
            'codigo' => 'required|string|max:20',
            'direccion' => 'required|string|max:255',
            'telefono' => 'required|string|max:30',
            'estado' => 'required',
            'id_tienda' => 'required'
        ], [
            'nombre.required' => 'El nombre de la tienda es requerido',
            'codigo.required' => 'El codigo es requerido',
            'direccion.required' => 'La direccion es requerida',
            'telefono.required' => 'El telefono es requerido'
        ]);

        DB::beginTransaction();

        $tienda = Tienda::find($validatedData['id_tienda']);
        $tienda->nombre = $validatedData['nombre'];
        $tienda->codigo = $validatedData['codigo'];
        $tienda->direccion = $validatedData['direccion'];
        $tienda->telefono = $validatedData['telefono'];
        $tienda->usuario_modifica = $request->user()->id;
        if ($validatedData['estado'] == 'Inactivo') {
            $tienda->estado = 'Activo';
        }
        
        if (!$tienda->save()) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error al Actualizar los datos de la tienda ');
        }

        DB::commit();
        return redirect()->route('stores')->with('success', 'Tienda Actualizada exitosamente');
    }

    /* DESACTIVAR TIENDA */
    public function deactivate(Request $request) {
        $validatedData = $request->validate([
            'id_tienda' => 'required'
        ]);

        DB::beginTransaction();
        $tienda = Tienda::find($validatedData['id_tienda']);
        $tienda->estado = 'Inactivo';
        $tienda->usuario_modifica = $request->user()->id;

        if (!$tienda->save()) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error al dar de baja la tienda');
        }

        DB::commit();
        return redirect()->route('stores')->with('success', 'La tienda fue dada de baja');
    }
}
