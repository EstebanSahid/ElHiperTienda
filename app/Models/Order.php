<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Order extends Model
{
    use HasFactory, Notifiable;

    public $timestamps = true;

    protected $table = 'pedidos';

    protected $primaryKey = 'id_pedido';

    protected $fillable = [
        'numero_pedido',
        'fecha_pedido',
        'estado',
        'id_tienda',
        'ucrea',
        'umodifica'
    ];
}
