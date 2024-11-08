<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenEncabezado extends Model
{

    use HasFactory;

    public $timestamps = true;

    protected $table = 'orden_encabezado';

    protected $primaryKey = 'id_encabezado';

    protected $fillable = [
        'id_tienda',
        'posicion',
    ];
}
