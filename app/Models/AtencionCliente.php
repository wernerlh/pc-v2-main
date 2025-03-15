<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AtencionCliente extends Model
{
    use HasFactory;

    protected $table = 'atencion_cliente';

    protected $primaryKey = 'id'; 
    protected $fillable = [
        'cliente_id',
        'empleado_id',
        'fecha_apertura',
        'fecha_cierre',
        'tipo',
        'asunto',
        'descripcion',
        'prioridad',
        'estado',
        'respuesta',
        'tiempo_respuesta',
        'calificacion',
        'comentario_calificacion',
    ];

    protected $casts = [
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(UserCliente::class, 'cliente_id');
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleados::class, 'empleado_id');
    }

}
