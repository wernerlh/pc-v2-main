<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaccionesFinanciera extends Model
{
    use HasFactory;

    protected $table = 'transacciones_financieras';

    protected $primaryKey = 'id'; 
    protected $fillable = [
        'cliente_id',
        'monto',
        'tipo',
        'estado',
        'numero_cuenta_bancaria',
        'banco',
        'titular_cuenta',
        'referencia_transferencia',
        'fecha_solicitud',
        'fecha_procesamiento',
        'motivo_rechazo',

        'revisado_por',
    ];

    protected $casts = [
        'fecha_solicitud' => 'datetime',
        'fecha_procesamiento' => 'datetime',

    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(UserCliente::class, 'cliente_id');
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleados::class, 'revisado_por');
    }
}
