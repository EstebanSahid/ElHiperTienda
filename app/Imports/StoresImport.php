<?php

namespace App\Imports;

use App\Models\Tienda;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StoresImport implements ToModel, WithHeadingRow
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

        if (!isset($row['codigo']) || trim($row['codigo']) === '') {
            $this->errors[] = "Falta la columna 'codigo' en una fila del archivo o está vacía.";
            return null;
        }

        if (!isset($row['nombre']) || trim($row['nombre']) === '') {
            $this->errors[] = "Falta la columna 'nombre' en una fila del archivo o está vacía.";
            return null;
        }

        $codigo = trim($row['codigo']);
        
        if (Tienda::where('codigo', $codigo)->exists()) {
            $this->noRegistrados[] = "La tienda ya existe: {$codigo} - {$row['nombre']}";
            return null;
        }

        return new Tienda([
            'nombre'     => $row['nombre'],
            'codigo'     => $codigo,
            'direccion'  => $row['direccion'] ?? null,
            'telefono'   => $row['telefono'] ?? null,
            'estado'     => 'Activo',
            'usuario_crea'      => $this->UserId,
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
            return "Hubo un total de $totalNoRegistrados tiendas no registradas.";
        }

        return implode(', ', $this->noRegistrados);
    }

    public function getErrores()
    {
        return $this->errors;
    }
}
