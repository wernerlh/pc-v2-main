<?php

namespace App\Providers\Filament;

use App\Http\Middleware\VerificarCuentaActiva;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Usuariocasino\Pages\Auth\RegisterUserCliente;
use App\Filament\Usuariocasino\Pages\DatosPersonales;
use App\Filament\Usuariocasino\Pages\CambiarCorreo;
use App\Filament\Usuariocasino\Pages\CambiarContrasena;
use App\Filament\Usuariocasino\Pages\JuegosCasino;
use App\Filament\Usuariocasino\Pages\MisDepositos;
use App\Filament\Usuariocasino\Pages\MisRetiros;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class UsuariocasinoPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('usuariocasino')
            ->path('usuariocasino')
            ->login()
            ->registration(RegisterUserCliente::class) // Usar la clase correcta
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Usuariocasino/Resources'), for: 'App\\Filament\\Usuariocasino\\Resources')
            ->discoverPages(in: app_path('Filament/Usuariocasino/Pages'), for: 'App\\Filament\\Usuariocasino\\Pages')
            ->pages([

                DatosPersonales::class,
                CambiarCorreo::class,
                CambiarContrasena::class,
                JuegosCasino::class,
                MisDepositos::class,
                MisRetiros::class,

            ])
            ->discoverWidgets(in: app_path('Filament/Usuariocasino/Widgets'), for: 'App\\Filament\\Usuariocasino\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                VerificarCuentaActiva::class,

            ])
            ->authMiddleware([
                Authenticate::class,
                VerificarCuentaActiva::class,

            ])
            ->authGuard('cliente');
    }
}