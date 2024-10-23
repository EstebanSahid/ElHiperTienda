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
        $users = DB::table('users as u')
        ->join('roles as r', 'u.id_rol', '=', 'r.id_rol')
        ->select('u.id', 'u.name', 'r.descripcion', 'u.email', 'u.telefono', 'u.estado')
        ->orderBy('r.descripcion')
        ->orderBy('u.name')
        ->paginate(5);

        return Inertia::render('Admin/Users/Users', [
            'users' => $users,
        ]);
    }

    /* NUEVO USUARIO */
    public function create() {
        $roles = Rol::all();

        /*
        $tiendas = DB::table('tienda')
            ->select('id_tienda', DB::raw('CONCAT("Tienda: ", codigo, " - ", nombre) as nombre_tienda'))
            ->where('estado', 'Activo')
            ->orderBy('codigo')
            ->get();
        */

        $tiendas = $this->getTiendas();

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

    /* EDITAR USUARIO */
    public function renderEdit(Request $request, $id) {
        $roles = Rol::all();
        $tiendas = $this->getTiendas();

        $user = $this->getUser($id);
        $accesos = $this->getAccesos($id);
        //dd($accesos);
        return Inertia::render('Admin/Users/EditUser', [
            'roles' => $roles,
            'tiendas' => $tiendas,
            'accesos' => $accesos,
            'user' => $user
        ]);
    }

    public function update(Request $request) {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255',
            'telefono' => 'required|string|max:30',
            'id_rol' => 'required',
            'id_user' => 'required'
        ]);

        DB::beginTransaction();
        $user = User::find($validatedData['id_user']);
        $user->name = $validatedData['name'];
        $user->telefono = $validatedData['telefono'];
        $user->id_rol = $validatedData['id_rol'];

        if (!$user->save()) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error al Actualizar los datos del usuario.']);
        }

        if ($validatedData['id_rol'] == 1) {
            
        }


        //dd($user);
    }

    private function getUser($id) {
        return DB::table('users as u')
            ->join('roles as r', 'r.id_rol', '=', 'u.id_rol')
            ->select('u.id', 'u.name', 'u.telefono', 'u.email', 'u.id_rol', 'r.descripcion')
            ->where('id', $id)
            ->first();
    }

    private function getTiendas() {
        return DB::table('tienda')
        ->select('id_tienda', DB::raw('CONCAT("Tienda: ", codigo, " - ", nombre) as nombre_tienda'))
        ->where('estado', 'Activo')
        ->orderBy('codigo')
        ->get();
    }

    private function getAccesos($id) {
        return DB::table('accesos')
            ->where('id_user', $id)
            ->pluck('id_tienda');
    }
}
