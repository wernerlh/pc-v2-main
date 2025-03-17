<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class JuegosOnline extends Model
{
    use HasFactory;

    protected $table = 'juegos_online';

    protected $fillable = [
        'nombre',
        'pagina_juego',
        'imagen_url',
        'categoria_id',
        'descripcion',
        'estado',
        'membresia_requerida',
    ];

    protected static function booted(): void
    {
        static::saving(function ($juegoOnline) {
            // Si no hay pagina_juego definida, generarla a partir del nombre
            if (empty($juegoOnline->pagina_juego)) {
                // Convertir el nombre a kebab case y generar la ruta
                $slug = Str::of($juegoOnline->nombre)->kebab();
                $juegoOnline->pagina_juego = 'filament.usuariocasino.pages.' . $slug;
            }
        });
    }

    public function categoriaJuego(): BelongsTo
    {
        return $this->belongsTo(CategoriasJuego::class, 'categoria_id');
    }
    // Añadir esta relación como alias para mantener compatibilidad
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriasJuego::class, 'categoria_id');
    }
    public function membresiaRequerida(): BelongsTo
    {
        return $this->belongsTo(Membresia::class, 'membresia_requerida');
    }
}
