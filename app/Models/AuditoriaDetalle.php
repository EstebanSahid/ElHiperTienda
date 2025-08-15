<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditoriaDetalle extends Model
{
    public $timestamps = true;

    protected $table = 'audit_details';

    protected $primaryKey = 'id_auditoria_detalle';

    protected $fillable = [
        'id_auditoria',
        'campo',
        'valor_anterior',
        'valor_nuevo',
        'cambios_dataJson'
    ];

    public function auditoria() {
        return $this->belongsTo(Auditoria::class, 'id_auditoria');
    }
}
