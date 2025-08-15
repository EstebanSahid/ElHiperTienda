<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    public $timestamps = true;

    protected $table = 'audits';

    protected $primaryKey = 'id_auditoria';

    protected $fillable = [
        'tipo_auditable',
        'id_auditoria_registro',
        'evento',
        'usuario_crea',
        'descripcion',
        'direccion_ip',
        'user_agent',
    ];

    public function details() {
        return $this->hasMany(AuditoriaDetalle::class, 'id_auditoria');
    }

    public function user() {
        return $this->belongsTo(User::class, 'usuario_crea');
    }
}
