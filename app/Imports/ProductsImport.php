<?php

namespace App\Imports;

use App\Models\Producto;
use Maatwebsite\Excel\Concerns\ToModel;
use PhpParser\Node\Stmt\Return_;

class ProductsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        dd($this->validarCabeceras($row));
        if ($this->validarCabeceras($row)) {
            return null;
        }

        
        return new Producto([
            //
        ]);
    }

    private function validarCabeceras($cabeceras) {
        $cabecerasEsperadas = ['plus','nombre'];

        $diferencias = array_diff($cabecerasEsperadas, $cabeceras);

        if (!empty($diferencias)) {
            // throw new \Exception('Faltan las siguientes columnas: ' . implode(', ', $diferencias));
            return false;
        }
    
        return true;
    }
}
