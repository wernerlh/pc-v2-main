<?php

namespace App\Filament\Usuariocasino\Pages;

use App\Models\UserCliente;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;


class CambiarCorreo extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $title = 'Cambiar Correo Electrónico';
    protected static ?string $navigationLabel = 'Cambiar Correo';
    protected static ?string $navigationGroup = 'Perfil';

    protected static string $view = 'filament.usuariocasino.pages.cambiar-correo';

    public ?array $data = [];

    public function mount(): void
    {
        $user = Auth::guard('cliente')->user();

        $this->form->fill([
            'email_actual' => $user->email,
            'email_nuevo' => '',
            'password' => '',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Cambiar Correo Electrónico')
                    ->schema([
                        TextInput::make('email_actual')
                            ->email()
                            ->disabled()
                            ->label('Correo Electrónico Actual'),
                        TextInput::make('email_nuevo')
                            ->email()
                            ->required()
                            ->unique(UserCliente::class, 'email')
                            ->label('Nuevo Correo Electrónico'),

                    ]),
                Section::make('Verificación de Seguridad')

                    ->schema([
                        TextInput::make('password')
                            ->password()
                            ->required()
                            ->label('Contraseña')
                            ->helperText('Para guardar los cambios, debe ingresar su contraseña actual.'),
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
        if (!$user || !Hash::check($data['password'], $user->password)) {
            Notification::make()
                ->title('Contraseña incorrecta')
                ->danger()
                ->send();

            return;
        }

        if ($user) {
            // Actualizar el correo electrónico
            $user->email = $data['email_nuevo'];
            $user->save();

            // Mostrar notificación de éxito
            Notification::make()
                ->title('Correo electrónico actualizado con éxito')
                ->success()
                ->send();

            // Resetear el formulario con el nuevo correo
            $this->form->fill([
                'email_actual' => $user->email,
                'email_nuevo' => '',
                'password' => '',
            ]);
        }
    }
}
