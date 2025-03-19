<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portada extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'titulo',
        'imagen_url',
        'estado',
        'orden',
    ];
    
    /**
     * Verifica si la portada está activa
     */
    public function isActive(): bool
    {
        return $this->estado === 'activo';
    }
    
    /**
     * Scope para filtrar portadas activas
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', 'activo');
    }
}