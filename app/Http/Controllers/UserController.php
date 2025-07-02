<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\http\Controllers\PermisosController;
use App\Models\Access;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;


class UserController extends Controller
{
    /* INDEX */
    public function index() {
        try{
            $users = DB::table('users as u')
            ->join('roles as r', 'u.id_rol', '=', 'r.id_rol')
            ->select('u.id', 'u.name', 'r.descripcion', 'u.email', 'u.telefono', 'u.estado')
            ->orderBy('r.descripcion')
            ->orderBy('u.name')
            ->paginate(5);
    
            return Inertia::render('Admin/Users/Users', [
                'users' => $users,
            ]);

        } catch (\Exception $e) {
            Log::error('Error al cargar los usuarios: ' . $e->getMessage());
            
            return Inertia::render('Admin/Users/Users', [
                'users' => [],
                'error' => [
                    'mensaje' => 'Error al cargar los usuarios: ' . $e->getMessage(),
                    'duracionNotificacion' => 10
                ]
            ], 500);
        }
    }

    /* NUEVO USUARIO */
    public function create() {
        $roles = Rol::all();
        $tiendas = $this->getTiendas();

        return Inertia::render('Admin/Users/RegisterUser', [
            'roles' => $roles,
            'tiendas' => $tiendas
        ]);
    }

    public function store(Request $request) {
        try {
            if(!$this->validarPermisosUsuario($request->user())) {
                return redirect('dashboard')->with('error', 'No tienes permisos para realizar esta acción');
            }

            // Validación
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
                'password' => ['required', 'confirmed'],
                'telefono' => 'required|string|max:30',
                'id_rol' => 'required',
            ], [
                'name.required' => 'El nombre es requerido',
                'email.required' => 'El email es requerido',
                'email.lowercase' => 'El email debe estar en minusculas',
                'email.email' => 'El email debe estar en el formato adecuado',
                'email.unique' => 'Este correo ya existe, por favor utilice otro',
                'password.required' => 'La contraseña es requerida',
                'password.confirmed' => 'Las contraseñas no coinciden',
                'telefono.required' => 'El telefono es requerido',
                'id_rol.required' => 'El rol es requerido'
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
                        return redirect()->back()->withInput()->with('error', 'Error al guardar los permisos.');
                    }
                }
            } 
            
            DB::commit();

            return redirect()->route('users')->with('success', 'Usuario creado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar la orden:' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error al guardar la orden:' . $e->getMessage());
        }
    }

    /* EDITAR USUARIO */
    public function renderEdit(Request $request, $id) {
        try {
            // Validacion de rol de usuario
            if(!$this->validarPermisosUsuario($request->user())) {
                return redirect('dashboard')->with('error', 'No tienes permisos para realizar esta acción');
            }

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
        } catch (\Exception $e) {
            Log::error('Error al cargar la vista de editar usuario: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error al cargar la vista de editar usuario: ' . $e->getMessage());
        }
    }

    public function update(Request $request) {
        try {
            if(!$this->validarPermisosUsuario($request->user())) {
                return redirect('dashboard')->with('error', 'No tienes permisos para realizar esta acción');
            }

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|lowercase|email|max:255',
                'telefono' => 'required|string|max:30',
                'id_rol' => 'required',
                'id_user' => 'required',
                'estado' => 'required|string'
            ], [
                'name.required' => 'El nombre es requerido',
                'email.required' => 'El email es requerido',
                'email.lowercase' => 'El email debe estar en minusculas',
                'email.email' => 'El email debe estar en el formato adecuado',
                'telefono.required' => 'El telefono es requerido',
                'id_rol.required' => 'El rol es requerido'
            ]);

            DB::beginTransaction();

            $user = User::find($validatedData['id_user']);
            $user->name = $validatedData['name'];
            $user->telefono = $validatedData['telefono'];
            $user->id_rol = $validatedData['id_rol'];
            
            if($validatedData['estado'] == 'Inactivo') {
                $user->estado = 'Activo';
            }

            if (!$user->save()) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('error', 'Error al actualizar los datos del usuario.');
            }

            // if ($validatedData['id_rol'] == 1) {
            if (PermisosController::esAdministradorSegunId($validatedData['id_rol'])) {
                DB::table('accesos')->where('id_user', $user->id)->delete();
            } else {
                DB::table('accesos')->where('id_user', $user->id)->delete();

                foreach ($request->tiendasAsignadas as $permiso) {
                    $acceso = new Access();
                    $acceso->id_user = $user->id;
                    $acceso->id_tienda = $permiso;
                    $acceso->estado = 'Activo';
                    $acceso->usuario_crea = $request->user()->id;
                    if (!$acceso->save()) {
                        DB::rollBack();
                        return redirect()->back()->withInput()->with('error', 'Error al guardar los permisos.');
                    }
                }
            }
            
            DB::commit();

            return redirect()->route('users')->with('success', 'Usuario Actualizado exitosamente');
        } catch (\Exception $e) {
            // En caso de error, revertimos la transacción
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Hubo un error: ' . $e->getMessage());
        }

    }

    private function getUser($id) {
        try {
            return DB::table('users as u')
                ->join('roles as r', 'r.id_rol', '=', 'u.id_rol')
                ->select('u.id', 'u.name', 'u.telefono', 'u.email', 'u.id_rol', 'u.estado', 'r.descripcion')
                ->where('id', $id)
                ->first();
        } catch (\Exception $e) {
            Log::error('Error al obtener el usuario ID:' . $id . ' error::' . $e->getMessage());
            throw $e;
        }
    }

    private function getTiendas() {
        try {
            return DB::table('tienda')
                ->select('id_tienda', DB::raw('CONCAT("Tienda: ", codigo, " - ", nombre) as nombre_tienda'))
                ->where('estado', 'Activo')
                ->orderBy('codigo')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error al obtener las tiendas::' . $e->getMessage());
            throw $e;
        }
    }

    private function getAccesos($id) {
        try {
            return DB::table('accesos')
                ->where('id_user', $id)
                ->pluck('id_tienda');
        } catch (\Exception $e) {
            Log::error('Error al obtener los accesos del usuario ID:' . $id . ' error::' . $e->getMessage());
            throw $e;
        }
    }

    /* DESACTIVAR USUARIO (BORRAR USUARIO) */
    public function deactivate(Request $request) {
        // Verificar rol del usuario
        try {
            if(!$this->validarPermisosUsuario($request->user())) {
                return redirect('dashboard')->with('error', 'No tienes permisos para realizar esta acción');
            }
    
            $validatedData = $request->validate([
                'id_user' => 'required'
            ]);
    
            DB::beginTransaction();
            $user = User::find($validatedData['id_user']);
            $user->estado = 'Inactivo';
    
            DB::table('sessions')
                ->where('user_id', $validatedData['id_user'])
                ->delete();
    
            if (!$user->save()) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('error', 'Error al dar de baja al usuario');
            }
    
            DB::commit();
            
            return redirect()->route('users')->with('success', 'El usuario fue dado de baja');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al deshabilitar al usuario: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error al dar de baja al usuario');
        }
    }

    private function validarPermisosUsuario(User $user): bool {
        // Verificar si el usuario tiene permisos para realizar la acción
        return PermisosController::esAdministrador($user);
    }
}
