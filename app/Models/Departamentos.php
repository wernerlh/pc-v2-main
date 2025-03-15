<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departamentos extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'departamentos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'gerente_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the gerente that owns the departamento.
     */
    public function gerente()
    {
        return $this->belongsTo(Empleados::class, 'gerente_id');
    }

    /**
     * Get the empleados that belong to the departamento.
     */
    public function empleados()
    {
        return $this->hasMany(Empleados::class, 'departamento_id');
    }
}
