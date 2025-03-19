<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AtencionEmpleado extends Model
{
    use HasFactory;

    protected $table = 'atencion_empleados';
    
    protected $fillable = [
        'empleado_id',
        'supervisor_id',
        'asunto',
        'descripcion',
        'estado',
        'prioridad',
        'fecha_solicitud',
        'fecha_atencion',
        'fecha_resolucion',
        'solucion',
    ];
    
    protected $casts = [
        'fecha_solicitud' => 'datetime',
        'fecha_atencion' => 'datetime',
        'fecha_resolucion' => 'datetime',
    ];
    
    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleados::class, 'empleado_id');
    }
    
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Empleados::class, 'supervisor_id');
    }
    
    // Getters para mostrar textos amigables de los enums
    public function getEstadoTextoAttribute(): string
    {
        return match ($this->estado) {
            'pendiente' => 'Pendiente',
            'en_proceso' => 'En Proceso',
            'resuelto' => 'Resuelto',
            'cancelado' => 'Cancelado',
            default => $this->estado,
        };
    }
    
    public function getPrioridadTextoAttribute(): string
    {
        return match ($this->prioridad) {
            'baja' => 'Baja',
            'media' => 'Media',
            'alta' => 'Alta',
            'urgente' => 'Urgente',
            default => $this->prioridad,
        };
    }
}