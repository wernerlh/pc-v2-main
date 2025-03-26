<!-- ==========Header Section Starts Here========== -->
<header class="header-section">
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