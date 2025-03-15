<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaccionesJuego extends Model
{
    use HasFactory;

    protected $table = 'transacciones_juego';

    protected $fillable = [
        'cliente_id',
        'juego_id',
        'fecha_hora',
        'monto_apostado',
        'monto_ganado',
        'tipo_transaccion',
        'balance_anterior',
        'balance_posterior',
        'detalles_juego',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'detalles_juego' => 'json',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(UserCliente::class, 'cliente_id');
    }

    public function juego(): BelongsTo
    {
        return $this->belongsTo(JuegosOnline::class, 'juego_id');
    }
}
