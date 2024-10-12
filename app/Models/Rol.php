<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{

    use HasFactory;

    protected $table = 'roles';

    protected $primary_key = 'id_rol';

    protected $fillable = [
        'descripcion',
        'estado'
    ];
}
