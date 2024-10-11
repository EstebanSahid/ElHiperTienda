<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Tienda extends Model
{

    use HasFactory, Notifiable;

    public $timestamps = true;

    protected $table = "tienda";

    protected $primaryKey = 'id_tienda';

    protected $fillable = [
        'id_tienda',
        'nombre',
        'codigo',
        'direccion',
        'telefono',
        'estado',
        'usuario_crea',
        'usuario_modifica',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'accesos', 'id_tienda', 'id_user');
    }
}
