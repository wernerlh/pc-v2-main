<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;




use Illuminate\Database\Eloquent\Model;

class TransaccionesCasinoP extends Model
{

    use HasFactory;
    
    protected $table = 'transacciones_casinop';
    
    protected $fillable = [
        'cliente_id',
        'empleado_id',
        'sucursal_id',
        'fecha',
        'tipo',
        'monto',
        'observacion',
    ];
    
    protected $casts = [
        'fecha' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(UserCliente::class, 'cliente_id');
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleados::class, 'empleado_id', 'empleado_id');
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursales::class, 'sucursal_id');
    }

    public function getTipoTextoAttribute(): string
    {
        return $this->tipo === 'deposito' ? 'Depósito' : 'Retiro';
    }

}
