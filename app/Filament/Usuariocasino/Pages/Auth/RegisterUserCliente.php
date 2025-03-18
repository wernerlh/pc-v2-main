<?php

namespace App\Filament\Usuariocasino\Pages\Auth;

use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Models\UserCliente;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;

class RegisterUserCliente extends BaseRegister
{
    protected static ?string $guard = 'cliente';

    protected static string $model = UserCliente::class;

    public function form(Form $form): Form
    {

        return $form
            ->schema([
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(UserCliente::class, 'email')
                    ->maxLength(100)
                    ->label('Correo Electronico'),
                TextInput::make('name')
                    ->required()
                    ->minLength(8)
                    ->maxLength(16)
                    ->regex('/^(?=.*[a-z])(?=.*[0-9])[a-z0-9]{8,16}$/')
                    ->label('Nombre de Usuario')
                    ->unique(UserCliente::class, 'name')
                    ->helperText('Debe tener al menos 8 caracteres, incluir al menos una letra minúscula y un número (no debe contener espacios).'),
                TextInput::make('nombre_completo')
                    ->required()
                    ->maxLength(200)
                    ->label('Nombre Completo'),
                TextInput::make('telefono')
                    ->unique(UserCliente::class, 'telefono')
                    ->required()
                    ->regex('/^[0-9]{9}$/')
                    ->label('Telefono')
                    ->helperText('Debe contener exactamente 9 dígitos numéricos.'),
                TextInput::make('direccion')
                    ->nullable()
                    ->required()
                    ->maxLength(200)
                    ->label('Direccion'),
                DatePicker::make('fecha_nacimiento')
                    ->required()
                    ->label('Fecha de Nacimiento')
                    ->maxDate(now()->subYears(18)) // Fecha máxima es hoy menos 18 años
                    ->helperText('Debes tener al menos 18 años para registrarte.'),
                TextInput::make('documento_identidad')
                    ->unique(UserCliente::class, 'documento_identidad')
                    ->required()
                    ->regex('/^[0-9]{8}$/')
                    ->maxLength(8)
                    ->label('Documento de identidad')
                    ->helperText('Debe contener exactamente 8 dígitos numéricos.'),
                TextInput::make('password')
                    ->password()
                    ->required()
                    ->label('Contraseña')
                    ->rule(Password::min(8)->mixedCase()->numbers()->symbols())
                    ->helperText('La contraseña debe tener al menos 8 caracteres, incluir mayúsculas, minúsculas, números y símbolos.'),
                TextInput::make('password_confirmation')
                    ->password()
                    ->required()
                    ->same('password')
                    ->label('Confirmar Contraseña')
            ]);
    }

    protected function create(array $data): UserCliente
    {
        return UserCliente::create([
            'name' => $data['name'],
            'nombre_completo' => $data['nombre_completo'],
            'telefono' => $data['telefono'],
            'direccion' => $data['direccion'],
            'fecha_nacimiento' => $data['fecha_nacimiento'],
            'documento_identidad' => $data['documento_identidad'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    protected function validator(array $data): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'min:8', 'max:16', 'regex:/^(?=.*[a-z])(?=.*[0-9])[a-z0-9]{8,16}$/'],
            'nombre_completo' => ['required', 'string', 'max:200'],
            'telefono' => ['required', 'string', 'regex:/^[0-9]{9}$/'],
            'direccion' => ['nullable', 'string', 'max:200'],
            'fecha_nacimiento' => ['required', 'date'],
            'documento_identidad' => ['required', 'string', 'max:20', 'unique:user_clientes,documento_identidad'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:user_clientes,email'],
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols(), 'confirmed'],
        ]);
    }
}
