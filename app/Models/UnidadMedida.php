<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnidadMedida extends Model
{

    use HasFactory;

    public $timestamps = true;

    protected $table = "unidad_pedido";

    protected $primaryKey = "id_unidad_pedido";

    protected $fillable = [
        'descripcion',
        'codigo',
        'estado',
    ];
}
