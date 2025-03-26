<!-- ==========Header Section Starts Here========== -->
<header class="header-section">
    @php
        $banner = \App\Models\Banner::getActiveBanner();

        // Mapeo de colores a clases de Bootstrap
        $colorClasses = [
            'primary' => 'bg-primary text-white',
            'secondary' => 'bg-secondary text-white',
            'success' => 'bg-success text-white',
            'warning' => 'bg-warning text-dark',
            'danger' => 'bg-danger text-white',
            'info' => 'bg-info text-dark',
        ];

        // Obtener la clase de color correspondiente o usar una por defecto
        $colorClass = isset($colorClasses[$banner->color ?? ''])
            ? $colorClasses[$banner->color]
            : 'bg-primary text-white';
    @endphp

    @if ($banner)
        <!-- Banner informativo -->
        <div class="banner-info {{ $colorClass }}" style="padding: 8px 0; text-align: center; font-weight: 500;">
            <div class="container">
                <div class="d-flex align-items-center justify-content-center">
                    @if ($banner->icono)
                        @if (str_starts_with($banner->icono, 'heroicon-'))
                            @svg($banner->icono, 'me-2', ['width' => 20, 'height' => 20])
                        @else
                            <i class="icofont-{{ $banner->icono }} me-2"></i>
                        @endif
                    @endif
                    <span>{!! $banner->contenido !!}</span>
                </div>
            </div>
        </div>
    @endif
    <div class="container">
        <div class="header-holder d-flex flex-wrap justify-content-between align-items-center">
            <div class="brand-logo d-none d-lg-inline-block">
                <div class="logo">
                    <a href="/">
                        <img src="assets/images/logo/logo.png" alt="logo">
                    </a>
                </div>
            </div>
            <div class="header-menu-part">
                <div class="header-bottom">
                    <div class="header-wrapper justify-content-lg-end">
                        <div class="mobile-logo d-lg-none">
                            <a href="/"><img src="assets/images/logo/logo.png" alt="logo"></a>
                        </div>
                        <div class="menu-area">
                            <ul class="menu">
                                <li>
                                    <a href="/">Inicio</a>

                                </li>
                                <li><a href="{{ route('membresias') }}">Membresías</a></li>

                                <li>
                                    <a href="{{ route('sedes') }}">Sedes</a>
                                </li>
                                <li><a href="{{ route('contactanos') }}">Contactanos</a></li>
                            </ul>
                            <a href="/usuariocasino" class="login"><i class="icofont-user"></i> <span>Ingresar</span>
                            </a>
                            <a href="/usuariocasino/register" class="signup"><i class="icofont-users"></i>
                                <span>Registrate</span></a>

                            <!-- toggle icons -->
                            <div class="header-bar d-lg-none">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                            <div class="ellepsis-bar d-lg-none">
                                <i class="icofont-info-square"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</header>
<!-- ==========Header Section Ends Here========== -->
