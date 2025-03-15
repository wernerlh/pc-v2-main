<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriasJuego extends Model
{
    use HasFactory;

    protected $table = 'categorias_juego';

    protected $fillable = [
        'nombre',
        'descripcion',
        'imagen_url',
        'orden',
        'estado',
    ];

    public function juegosOnline(): HasMany
    {
        return $this->hasMany(JuegosOnline::class, 'categoria_id');
    }
}
