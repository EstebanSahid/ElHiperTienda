<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Rol;
use App\Enums\RolEnum;

class PermisosController extends Controller
{
    public static function esAdministrador(User $user): bool {
        return $user->id_rol == RolEnum::ADMINISTRADOR;
    }

    // public static function esVendedor(User $user): bool {
    //     return $user->id_rol == RolEnum::VENDEDOR;
    // }

    public static function esUsuario(User $user): bool {
        return $user->id_rol == RolEnum::USUARIO;
    }
}
