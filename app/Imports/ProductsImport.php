<?php

namespace App\Imports;

use App\Models\Producto;
use app\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow
{
    private $noRegistrados = [];
    private $errors = [];
    private $UserId;    
    private $cabecerasEsperadas;

    public function __construct($UserId, $cabecerasEsperadas)
    {
        $this->UserId = $UserId;
        $this->cabecerasEsperadas = $cabecerasEsperadas;
    }

    public function model(array $row)
    {   
        // $this->verificarCabeceras($row);
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
            // $this->noRegistrados[] = "El producto no es válido: {$plus}, - {$row['nombre']}.";
            $this->noRegistrados[] = "El producto {$row['nombre']} tiene un 'plus' no válido: {$plus}";
            return null;
        }
        
        // Verifica si el 'plus' ya existe en la base de datos
        if (Producto::where('plus', $plus)->exists()) {
            $this->noRegistrados[] = "El plus ya existe: {$plus}, - {$row['nombre']}";
            return null;
        }
        
        // dd($this->noRegistrados);

        return new Producto([
            'plus' => $row['plus'],
            'nombre' => $row['nombre'],
            'estado' => 'Activo',
            'ucrea' => $this->UserId,
        ]);
    }

    public function getNoRegistrados()
    {
        return implode(', ', $this->noRegistrados);
    }

    public function getErrores()
    {
        return $this->errors;
    }

    private function verificarCabeceras($row)
    {
        foreach ($this->cabecerasEsperadas as $columna) {
            if (!array_key_exists($columna, $row)) {
                $this->errors[] = "Falta la columna '{$columna}' en una fila del archivo.";
                return null;
            }
        }
    }

}
