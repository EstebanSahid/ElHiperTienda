<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Access extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'accesos';

    protected $primary_key = "id_acceso";

    protected $fillable = [
        'id_user',
        'id_tienda',
        'estado',
        'usuario_crea',
        'usuario_modifica',
    ];
}
