<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserCliente extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'name',
        'nombre_completo',
        'documento_identidad',
        'telefono',
        'direccion',
        'fecha_nacimiento',
        'preferencias',
        'estado_cuenta',
        'fecha_suspension',
        'email',
        'password',
    ];

    public function membresia(): BelongsTo
    {
        return $this->belongsTo(Membresia::class, 'membresia_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'preferencias' => 'array',
    ];    

    /**
     * Obtiene las transacciones financieras del cliente
     */
    public function transaccionesFinancieras(): HasMany
    {
        return $this->hasMany(TransaccionesFinanciera::class, 'cliente_id');
    }
    
    /**
     * Evento created - crear automáticamente la billetera cuando se crea un cliente
     */
    protected static function booted()
    {
        static::created(function ($cliente) {
            // Crear billetera automáticamente al crear un cliente
            if (!$cliente->billetera) {
                BilleteraCliente::create([
                    'cliente_id' => $cliente->id,
                    'moneda' => 'PEN'
                ]);
            }
        });
    }
}
