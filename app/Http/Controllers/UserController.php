<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Access;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;


class UserController extends Controller
{
    /* INDEX */
    public function index() {
        return Inertia::render('Admin/Users/Users');
    }

    /* NUEVO USUARIO */
    public function create() {
        $roles = Rol::all();
        $tiendas = DB::select("
            SELECT 
                id_tienda, CONCAT('Tienda: ',codigo, ' - ',nombre) as nombre_tienda
            FROM tienda
            WHERE estado = :estado
                ORDER  BY codigo
        ", ['estado' => 'Activo']);

        return Inertia::render('Admin/Users/RegisterUser', [
            'roles' => $roles,
            'tiendas' => $tiendas
        ]);
    }

    public function store(Request $request) {

        // Validación
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed'],
            'telefono' => 'required|string|max:30',
            'id_rol' => 'required',
        ]);

        // Creamos el Usuario
        DB::beginTransaction();

        $user = new User();
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->password = Hash::make($request->password);
        $user->telefono = $validatedData['telefono'];
        $user->estado = 'Activo';
        $user->id_rol = $validatedData['id_rol'];
        $user->save();

        // Obtenemos el ID para los permisos
        $userId = $user->id;
        if(!empty($request->tiendasAsignadas)) {
            foreach ($request->tiendasAsignadas as $permiso) {
                $acceso = new Access();
                $acceso->id_user = $userId;
                $acceso->id_tienda = $permiso;
                $acceso->estado = 'Activo';
                $acceso->usuario_crea = $request->user()->id;
                if (!$acceso->save()) {
                    DB::rollBack();
                    return redirect()->back()->withErrors(['error' => 'Error al guardar los permisos.']);
                }
            }
        } 
        
        DB::commit();

        return redirect()->route('users')->with('success', 'Usuario creado exitosamente');
    }
}
