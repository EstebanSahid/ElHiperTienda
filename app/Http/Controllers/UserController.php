<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


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

        $user = new User();
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->password = Hash::make($request->password);
        $user->telefono = $validatedData['telefono'];
        $user->estado = 'Activo';
        $user->id_rol = $validatedData['id_rol'];

        $user->save();

        // Puedes devolver una respuesta, redirigir o mostrar un mensaje de éxito
        //return redirect()->route('usuarios.index')->with('success', 'Usuario creado exitosamente');
    }
}
