<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Membresia extends Model
{
    use HasFactory;

    protected $table = 'membresias';

    protected $fillable = [
        'nombre',
        'descripcion',
        'beneficios',
        'descuento_porcentaje',
        'precio',
    ];

    public function clienteMembresias(): HasMany
    {
        return $this->hasMany(ClienteMembresia::class, 'membresia_id');
    }
}
