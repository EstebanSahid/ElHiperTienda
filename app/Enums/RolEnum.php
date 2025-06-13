<?php

namespace App\Enums;

enum RolEnum: int
{
    case ADMINISTRADOR = 1;
    CASE USUARIO = 2;
    CASE VENDEDOR = 3;

    public function label(): string {
        return match($this) {
            self::ADMINISTRADOR => 'Administrador',
            self::USUARIO => 'Usuario',
            self::VENDEDOR => 'Vendedor',
        };
    }
}
