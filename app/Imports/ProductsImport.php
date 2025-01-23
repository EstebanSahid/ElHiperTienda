<?php

namespace App\Imports;

use App\Models\Producto;
use app\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow
{
    private array $noRegistrados = [];
    private array $errors = [];
    private int $UserId;
    private string $nombreHoja;

    public function __construct($UserId, $nombreHoja)
    {
        $this->UserId = $UserId;
        $this->nombreHoja = $nombreHoja;
    }

    public function model(array $row)
    {   
        if (!array_key_exists('plus', $row)) {
            $this->errors[] = "Falta la columna 'plus' en una fila del archivo.";
            return null;
        }
    
        if (!array_key_exists('nombre', $row)) {
            $this->errors[] = "Falta la columna 'nombre' en una fila del archivo.";
            return null;
        }

        $plus = trim($row['plus']);
        
        // Validación del campo 'plus'
        if (!is_numeric($plus)) {
            $this->noRegistrados[] = "El producto {$row['nombre']} tiene un 'plus' no válido: {$plus}";
            return null;
        }
        
        // Verifica si el 'plus' ya existe en la base de datos
        if (Producto::where('plus', $plus)->exists()) {
            $this->noRegistrados[] = "El plus ya existe: {$plus}, - {$row['nombre']}";
            return null;
        }

        return new Producto([
            'plus' => $row['plus'],
            'nombre' => $row['nombre'],
            'estado' => 'Activo',
            'ucrea' => $this->UserId,
        ]);
    }

    public function sheets(): array
    {
        return [
            $this->nombreHoja => $this,
        ];
    }

    public function getNoRegistrados()
    {
        $totalNoRegistrados = count($this->noRegistrados);

        if ($totalNoRegistrados > 10) {
            return "Hubo un total de $totalNoRegistrados productos no registrados.";
        }

        return implode(', ', $this->noRegistrados);
    }

    public function getErrores()
    {
        return $this->errors;
    }
}
