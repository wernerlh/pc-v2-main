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
use Illuminate\Validation\Rules\Password;

class CambiarContrasena extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';
    protected static ?string $title = 'Cambiar Contraseña';
    protected static ?string $navigationLabel = 'Cambiar Contraseña';
    protected static ?string $navigationGroup = 'Perfil';
    
    protected static string $view = 'filament.usuariocasino.pages.cambiar-contrasena';
    
    public ?array $data = [];
    
    public function mount(): void
    {
        $this->form->fill();
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Cambiar Contraseña')
                    ->schema([
                        Forms\Components\TextInput::make('password_actual')
                            ->password()
                            ->required()
                            ->label('Contraseña Actual')
                            ->helperText('Ingrese su contraseña actual.'),
                        Forms\Components\TextInput::make('password_nueva')
                            ->password()
                            ->required()
                            ->label('Nueva Contraseña')
                            ->rule(Password::min(8)->mixedCase()->numbers()->symbols())
                            ->helperText('La contraseña debe tener al menos 8 caracteres, incluir mayúsculas, minúsculas, números y símbolos.'),
                        Forms\Components\TextInput::make('password_confirmacion')
                            ->password()
                            ->required()
                            ->label('Confirmar Nueva Contraseña')
                            ->same('password_nueva')
                            ->helperText('Repita la nueva contraseña.'),
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
        
        // Verificar que la contraseña actual sea correcta
        if (!$user || !Hash::check($data['password_actual'], $user->password)) {
            Notification::make()
                ->title('Contraseña actual incorrecta')
                ->danger()
                ->send();
                
            return;
        }
        
        // Verificar que la nueva contraseña sea diferente a la actual
        if (Hash::check($data['password_nueva'], $user->password)) {
            Notification::make()
                ->title('La nueva contraseña debe ser diferente a la actual')
                ->danger()
                ->send();
                
            return;
        }
        
        if ($user) {
            // Actualizar la contraseña
            $user->password = Hash::make($data['password_nueva']);
            $user->save();
            
            // Mostrar notificación de éxito
            Notification::make()
                ->title('Contraseña actualizada con éxito')
                ->success()
                ->send();
                
            // Resetear el formulario
            $this->form->fill();
        }
    }
}