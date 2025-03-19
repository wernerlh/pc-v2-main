<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'icono',
        'contenido',
        'color',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
    /**
     * El método boot se ejecuta cuando se inicia el modelo
     */
    protected static function boot()
    {
        parent::boot();

        // Este evento se dispara antes de guardar un banner
        static::saving(function ($banner) {
            // Si el banner que estamos guardando se está activando
            if ($banner->activo) {
                // Desactivamos todos los demás banners
                self::where('id', '!=', $banner->id)->update(['activo' => false]);
            }
        });
    }

    /**
     * Obtiene el banner activo actualmente
     */
    public static function getActiveBanner()
    {
        return self::where('activo', true)->first();
    }
}
