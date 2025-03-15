<?php

namespace App\Filament\Usuariocasino\Pages\Auth;

use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Forms;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Models\UserCliente;
use Filament\Forms\Form;

class RegisterUserCliente extends BaseRegister
{
    protected static ?string $guard = 'cliente';

    protected static string $model = UserCliente::class;

    public function form(Form $form): Form
    {

        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(16)
                    ->regex('/^[a-z0-9]{8,16}$/')
                    ->label('Nombre de Usuario')
                    ->unique(UserCliente::class, 'name') ,
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(UserCliente::class, 'email')
                    ->maxLength(100)
                    ->label('Correo Electronico'),
                Forms\Components\TextInput::make('nombre_completo')
                    ->required()
                    ->maxLength(200)
                    ->label('Nombre Completo'),
                Forms\Components\TextInput::make('telefono')
                ->unique(UserCliente::class, 'telefono')
                    ->nullable()
                    ->required()
                    ->maxLength(15)
                    ->label('Telefono'),
                Forms\Components\TextInput::make('direccion')
                    ->nullable()
                    ->required()
                    ->maxLength(200)
                    ->label('Direccion'),
                Forms\Components\DatePicker::make('fecha_nacimiento')
                    ->required()
                    ->label('Fecha de Nacimiento'),
                Forms\Components\TextInput::make('documento_identidad')
                    ->unique(UserCliente::class, 'documento_identidad')
                    ->maxLength(20)
                    ->label('Documento de identidad')
                    ->required(),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->maxLength(255)
                    ->label('Contraseña'),
                Forms\Components\TextInput::make('password_confirmation')
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
            'name' => ['required', 'string', 'max:16', 'regex:/^[a-z0-9]{8,16}$/'],
            'nombre_completo' => ['required', 'string', 'max:200'],
            'telefono' => ['nullable', 'string', 'max:15'],
            'direccion' => ['nullable', 'string', 'max:200'],
            'fecha_nacimiento' => ['required', 'date'],
            'documento_identidad' => ['required', 'string', 'max:20', 'unique:user_clientes,documento_identidad'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:user_clientes,email'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ]);
    }
}
