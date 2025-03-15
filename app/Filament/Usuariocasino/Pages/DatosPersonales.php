<?php

namespace App\Filament\Usuariocasino\Pages;

use App\Models\UserCliente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DatosPersonales extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $title = 'Datos Personales';
    protected static ?string $navigationLabel = 'Datos Personales';
    protected static ?string $navigationGroup = 'Perfil';

    // Define la ruta del archivo de vista
    protected static string $view = 'filament.usuariocasino.pages.datos-personales';

    // Variable para almacenar los datos del formulario
    public ?array $data = [];

    public function mount(): void
    {
        // Obtener el usuario autenticado
        $user = Auth::guard('cliente')->user();

        // Rellenar el formulario con los datos del usuario
        $this->form->fill([
            'nombre_completo' => $user->nombre_completo,
            'telefono' => $user->telefono,
            'direccion' => $user->direccion,
            'fecha_nacimiento' => $user->fecha_nacimiento,
            'documento_identidad' => $user->documento_identidad,
            'password_actual' => '', // Campo nuevo para la contraseña actual

        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre_completo')
                    ->required()
                    ->maxLength(200)
                    ->label('Nombre Completo'),
                Forms\Components\TextInput::make('telefono')
                    ->unique(UserCliente::class, 'telefono', ignoreRecord: true)
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
                    ->unique(UserCliente::class, 'documento_identidad', ignoreRecord: true)
                    ->maxLength(20)
                    ->label('Documento de identidad')
                    ->required(),
                Forms\Components\Section::make('Verificación de Seguridad')
                    ->schema([
                        Forms\Components\TextInput::make('password_actual')
                            ->password()
                            ->required()
                            ->label('Contraseña Actual')
                            ->helperText('Para guardar los cambios, debe ingresar su contraseña actual.')
                    ]),
            ])
            ->statePath('data');
    }

    public function save()
    {
        // Validar y obtener los datos del formulario
        $data = $this->form->getState();
        
        // Obtener el usuario autenticado
        $userId = Auth::guard('cliente')->id();
        $user = UserCliente::find($userId);
        
        // Verificar que la contraseña ingresada sea correcta
        if (!$user || !Hash::check($data['password_actual'], $user->password)) {
            // Si la contraseña no coincide, mostrar un error
            Notification::make()
                ->title('Contraseña incorrecta')
                ->danger()
                ->send();
                
            // Detener la ejecución
            return;
        }
        
        if ($user) {
            // Actualizar los campos del usuario
            $user->nombre_completo = $data['nombre_completo'];
            $user->telefono = $data['telefono'];
            $user->direccion = $data['direccion'];
            $user->fecha_nacimiento = $data['fecha_nacimiento'];
            $user->documento_identidad = $data['documento_identidad'];
            
            // Guardar los cambios
            $user->save();
            
            // Mostrar notificación de éxito
            Notification::make()
                ->title('Datos actualizados con éxito')
                ->success()
                ->send();
        }
    }
}
