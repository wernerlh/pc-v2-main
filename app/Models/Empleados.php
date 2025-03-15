<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleados extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'empleados';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $primaryKey = 'empleado_id'; // Set the correct primary key
    protected $fillable = [
        'nombre_completo',
        'documento_identidad',
        'correo',
        'telefono',
        'cargo',
        'fecha_contratacion',
        'fecha_nacimiento',
        'estado',
        'salario_base',
        'supervisor_id',
        'sucursal_id',
        'departamento_id', // Agregado el campo departamento_id
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'fecha_contratacion' => 'date',
        'fecha_nacimiento' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the sucursal that owns the empleado.
     */
    public function sucursal()
    {
        return $this->belongsTo(Sucursales::class, 'sucursal_id');
    }

    /**
     * Get the supervisor that owns the empleado.
     */
    public function supervisor()
    {
        return $this->belongsTo(Empleados::class, 'supervisor_id');
    }

    /**
     * Get the empleados that belong to the supervisor.
     */
    public function empleados()
    {
        return $this->hasMany(Empleados::class, 'supervisor_id');
    }

    /**
     * Get the departamento that owns the empleado.
     */
    public function departamento()
    {
        return $this->belongsTo(Departamentos::class, 'departamento_id');
    }
}
