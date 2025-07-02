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

    // Método estático para obtener los enums como un array
    public static function values(): array {
        return [
            'ADMINISTRADOR' => self::ADMINISTRADOR->value,
            'USUARIO' => self::USUARIO->value,
            'VENDEDOR' => self::VENDEDOR->value,
        ];
    }
}
