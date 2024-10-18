<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'pedidos_detalle';

    protected $primaryKey = 'id_pdetalle';

    protected $fillable = [
        'nombre_producto',
        'plus_producto',
        'cantidad',
        'estado',
        'id_producto',
        'id_pedido',
        'id_unidad_pedido',
        'ucrea',
        'umodifica',
    ];
}
