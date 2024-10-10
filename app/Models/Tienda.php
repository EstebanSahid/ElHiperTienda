<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Tienda extends Model
{

    use HasFactory, Notifiable;

    public $timestamps = true;

    protected $fillable = [
        'id_user',
        'id_tienda',
        'estado',
        'usuario_crea',
        'usuario_modifica',
    ];
}
