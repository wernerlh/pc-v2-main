@extends('layouts.app')

@section('title', 'Inicio - casino')

@section('content')
    <!-- ===========Sección Banner comienza aquí========== -->
    <section class="banner" style="background-image: url({{ asset('assets/images/banner/bg.jpg') }});">
        <div class="container">
            <div class="row g-0">
                <div class="col-xl-6 col-lg-7 col-12">
                    <div class="banner__content">
                        <h3>el mejor sitio web</h3>
                        <h1>Casino Online</h1>
                        <h2>Transacciones con Dinero Real</h2>
                        <p>Transformamos la emoción del juego en experiencias digitales envolventes, combinando tecnología
                            de vanguardia con un diseño intuitivo para ofrecer la mejor plataforma de apuestas online.
                            Nuestro enfoque innovador maximiza la diversión mientras garantiza seguridad y fair play.</p> <a
                            href="login.html" class="default-button"><span>únete hoy <i
                                    class="icofont-play-alt-1"></i></span></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ===========Sección Banner termina aquí========== -->

    <!-- ===========Sección Colección comienza aquí========== -->
    <section class="collection-section padding-top padding-bottom">
        <div class="container">
            <div class="section-header">
                <h2>Bienvenido al Casino</h2>
            </div>
            <div class="section-wrapper game">
                <div class="row g-4 justify-content-center">
                    <div class="col-lg-4 col-sm-6 col-12">
                        <div class="game__item item-layer">
                            <div class="game__inner text-center">
                                <div class="game__thumb">
                                    <img src="assets/images/game/01.png" alt="imagen-juego">
                                </div>
                                <div class="game__content">
                                    <h4><a href="team-single.html">Consejos Expertos</a></h4>
                                    <p>Descubre estrategias probadas y consejos de expertos para maximizar tus ganancias en
                                        nuestros juegos de casino más populares.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 col-12">
                        <div class="game__item item-layer">
                            <div class="game__inner text-center">
                                <div class="game__thumb">
                                    <img src="assets/images/game/02.png" alt="imagen-juego">
                                </div>
                                <div class="game__content">
                                    <h4><a href="team-single.html">Premios Exclusivos</a></h4>
                                    <p>Accede a bonificaciones especiales, torneos con grandes premios y promociones
                                        diseñadas para jugadores como tú.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 col-12">
                        <div class="game__item item-layer">
                            <div class="game__inner text-center">
                                <div class="game__thumb">
                                    <img src="assets/images/game/03.png" alt="imagen-juego">
                                </div>
                                <div class="game__content">
                                    <h4><a href="team-single.html">Soporte 24/7</a></h4>
                                    <p>Nuestro equipo de atención al cliente está disponible en todo momento para resolver
                                        tus dudas y garantizar la mejor experiencia.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ===========Sección Colección termina aquí========== -->

    <!-- ===========Sección Banner Carrusel de Imágenes comienza aquí========== -->
    <section class="banner-carousel">
        @if (isset($portadas) && count($portadas) > 0)
            <div id="casinoBannerCarousel" class="carousel slide" data-bs-ride="carousel">
                <!-- Indicadores -->
                <div class="carousel-indicators">
                    @foreach ($portadas as $index => $portada)
                        <button type="button" data-bs-target="#casinoBannerCarousel" data-bs-slide-to="{{ $index }}"
                            class="{{ $index == 0 ? 'active' : '' }}" aria-current="{{ $index == 0 ? 'true' : '' }}"
                            aria-label="Slide {{ $index + 1 }}"></button>
                    @endforeach
                </div>

                <!-- Contenido del carrusel (imágenes dinámicas) -->
                <div class="carousel-inner">
                    @foreach ($portadas as $index => $portada)
                        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                            <div class="banner" style="background-image: url('{{ $portada->imagen_url }}');">
                                <div class="container">
                                    <div class="row g-0">
                                        <div class="col-xl-6 col-lg-7 col-12">
                                            <div class="banner__content">
                                                <h1>{{ $portada->titulo }}</h1>
                                                <a href="/usuariocasino/juegos-casino" class="default-button">
                                                    <span>¡Juega ahora! <i class="icofont-play-alt-1"></i></span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Controles -->
                <button class="carousel-control-prev" type="button" data-bs-target="#casinoBannerCarousel"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#casinoBannerCarousel"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Siguiente</span>
                </button>
            </div>
        @else
            <!-- Carrusel por defecto en caso no existan portadas activas -->
            <div id="casinoBannerCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#casinoBannerCarousel" data-bs-slide-to="0" class="active"
                        aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#casinoBannerCarousel" data-bs-slide-to="1"
                        aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#casinoBannerCarousel" data-bs-slide-to="2"
                        aria-label="Slide 3"></button>
                </div>

                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="banner" style="background-image: url({{ asset('assets/images/banner/bg-2.jpg') }});">
                            <div class="container">
                                <div class="row g-0">
                                    <div class="col-xl-6 col-lg-7 col-12">
                                        <div class="banner__content">
                                            <h3>el mejor sitio web</h3>
                                            <h1>Casino Online</h1>
                                            <h2>Juega y Gana</h2>
                                            <p>La mejor experiencia de juego en línea con pagos rápidos y seguros.</p>
                                            <a href="" class="default-button">
                                                <span>Regístrate ahora <i class="icofont-play-alt-1"></i></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="banner" style="background-image: url({{ asset('assets/images/banner/bg-3.jpg') }});">
                            <div class="container">
                                <div class="row g-0">
                                    <div class="col-xl-6 col-lg-7 col-12">
                                        <div class="banner__content">
                                            <h1>Bono de bienvenida</h1>
                                            <h2>200% en tu primer depósito</h2>
                                            <a href="" class="default-button">
                                                <span>Obtener bono <i class="icofont-gift"></i></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="banner" style="background-image: url({{ asset('assets/images/banner/bg-4.jpg') }});">
                            <div class="container">
                                <div class="row g-0">
                                    <div class="col-xl-6 col-lg-7 col-12">
                                        <div class="banner__content">
                                            <h1>Torneos especiales</h1>
                                            <h2>Cada fin de semana</h2>
                                            <a href="" class="default-button">
                                                <span>Participar <i class="icofont-circled-right"></i></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Controles estándar -->
                <button class="carousel-control-prev" type="button" data-bs-target="#casinoBannerCarousel"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#casinoBannerCarousel"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Siguiente</span>
                </button>
            </div>
        @endif
    </section>
    <!-- ===========Sección Banner Carrusel de Imágenes termina aquí========== -->

    <!-- ===========Sección Juegos Relacionados comienza aquí========== -->
    <div class="game-section padding-top padding-bottom overflow-hidden"
        style="background-image:url(assets/images/match/bg.jpg)">
        <div class="container">
            <div class="section-header">
                <h2>JUEGOS POPULARES</h2>
            </div>

            <!-- Filtro de categorías -->
            <ul class="game__filter">
                <li data-filter="*" class="is-checked"><span class="category">Todos</span></li>
                @foreach ($categorias as $categoria => $juegosEnCategoria)
                    <li data-filter=".cat-{{ $loop->iteration }}"><span class="category">{{ $categoria }}</span></li>
                @endforeach
            </ul>

            <!-- Grid de juegos -->
            <div class="row g-4 grid">
                @foreach ($categorias as $categoria => $juegosEnCategoria)
                    @foreach ($juegosEnCategoria as $juego)
                        <div class="col-lg-6 col-12 cat-{{ $loop->parent->iteration }}">
                            <div class="game__item item-layer">
                                <div class="game__inner text-center p-0">
                                    <div class="game__thumb mb-0 position-relative">
                                        @if (!empty($juego->imagen_url))
                                            <img src="{{ $juego->imagen_url }}" alt="{{ $juego->nombre }}"
                                                class="rounded-3 w-100">
                                        @else
                                            <img src="{{ asset('assets/images/game/01.jpg') }}"
                                                alt="{{ $juego->nombre }}" class="rounded-3 w-100">
                                        @endif

                                        @if ($juego->membresia_requerida)
                                            <span class="position-absolute top-0 end-0 badge bg-warning m-2">Premium</span>
                                        @endif
                                    </div>
                                    <div class="game__overlay">
                                        <div class="game__overlay-left">
                                            <h4>{{ $juego->nombre }}</h4>
                                            <p>Categoría: {{ $categoria }}</p>
                                        </div>
                                        <div class="game__overlay-right">
                                            <a href="/usuariocasino/juegos-casino" class="default-button">
                                                <span>jugar ahora <i class="icofont-circled-right"></i></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>

            <div class="button-wrapper text-center mt-5">
                <a href="/usuariocasino/juegos-casino" class="default-button">
                    <span>Ver Todos los Juegos <i class="icofont-circled-right"></i></span>
                </a>
            </div>
        </div>
    </div>
    <!-- ===========Sección Juegos Relacionados termina aquí========== -->

    <!-- ===========Sección Preguntas Frecuentes comienza aquí========== -->
    <section class="faq padding-top padding-bottom">
        <div class="container">
            <div class="row justify-content-center flex-row-reverse">
                <div class="col-lg-6 col-12">
                    <div class="faq-right-part">
                        <div class="faq-thumb">
                            <img src="assets/images/faq/01.png" alt="preguntas-frecuentes">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-12">
                    <div class="faq-left-part">
                        <div class="section-header text-start">
                            <h2>PREGUNTAS FRECUENTES</h2>
                            <p>¡Resolvemos todas tus dudas sobre nuestro casino online! Encuentra aquí información clara
                                sobre registros, pagos, bonos y cómo disfrutar al máximo de tu experiencia de juego.</p>
                        </div>
                        <div class="section-wrapper">
                            <ul class="accordion">
                                <li class="accordion-item">
                                    <div class="accordion-list">
                                        <div class="left">
                                            <div class="icon"></div>
                                        </div>
                                        <div class="right">
                                            <h6>01. ¿Cómo creo una cuenta en el casino?</h6>
                                        </div>
                                    </div>
                                    <div class="accordion-answer">
                                        <p>Registrarte es muy sencillo: haz clic en "Crear cuenta", completa tus datos
                                            personales, verifica tu correo electrónico y realiza tu primer depósito. El
                                            proceso completo toma menos de 3 minutos.</p>
                                    </div>
                                </li>
                                <li class="accordion-item">
                                    <div class="accordion-list in">
                                        <div class="left">
                                            <div class="icon"></div>
                                        </div>
                                        <div class="right">
                                            <h6>02. ¿Dónde puedo canjear mis ganancias?</h6>
                                        </div>
                                    </div>
                                    <div class="accordion-answer active">
                                        <p>Puedes retirar tus ganancias en la sección "Caja". Ofrecemos múltiples métodos de
                                            pago: transferencia bancaria, billeteras electrónicas y criptomonedas. Los
                                            retiros se procesan en un plazo máximo de 24 horas.</p>
                                    </div>
                                </li>
                                <li class="accordion-item">
                                    <div class="accordion-list">
                                        <div class="left">
                                            <div class="icon"></div>
                                        </div>
                                        <div class="right">
                                            <h6>03. ¿Cómo empiezo a jugar?</h6>
                                        </div>
                                    </div>
                                    <div class="accordion-answer">
                                        <p>Una vez registrado y con fondos en tu cuenta, simplemente elige tu juego favorito
                                            entre nuestras slots, juegos de mesa o apuestas deportivas. Todos los juegos
                                            tienen instrucciones claras para que empieces a divertirte inmediatamente.</p>
                                    </div>
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ===========Sección Preguntas Frecuentes termina aquí========== -->

    <!-- ===========Sección Testimonios comienza aquí========== -->
    <div class="testimonial padding-top padding-bottom" style="background-image:url(assets/images/testimonial/bg.png)">
        <div class="container">
            <div class="section-header">
                <h2>RESEÑAS DE NUESTROS JUGADORES</h2>
                <p>Descubre lo que dicen nuestros usuarios sobre su experiencia en el casino. Juegos emocionantes, pagos
                    rápidos y un servicio excepcional nos distinguen.</p>
            </div>
            <div class="section-wrapper">
                <div class="row g-4">
                    <div class="col-lg-6 col-12">
                        <div class="testimonial__thumb position-relative">
                            <img src="assets/images/testimonial/03.jpg" alt="testimonio">
                            <div class="video-icon">
                                <a href="https://www.youtube.com/embed/g5eQgEuiFC8" data-rel="lightcase">
                                    <i class="icofont-play-alt-1"></i>
                                    <span class="pluse"></span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-12">
                        <div class="testimonial-slider overflow-hidden">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <div class="testimonial-item">
                                        <div class="testimonial-inner">
                                            <div class="testimonial-head">
                                                <div class="testi-top">
                                                    <div class="testimonial-thumb">
                                                        <img src="assets/images/testimonial/01.jpg" alt="testimonio">
                                                    </div>
                                                    <div class="name-des">
                                                        <h5>Carlos Martínez</h5>
                                                        <p>Jugador VIP Nivel 5</p>
                                                    </div>
                                                </div>
                                                <div class="testimonial-footer">
                                                    <ul>
                                                        <li><i class="icofont-star"></i></li>
                                                        <li><i class="icofont-star"></i></li>
                                                        <li><i class="icofont-star"></i></li>
                                                        <li><i class="icofont-star"></i></li>
                                                        <li><i class="icofont-star"></i></li>
                                                    </ul>
                                                    <h5>5.0</h5>
                                                </div>
                                            </div>
                                            <div class="testimonial-body">
                                                <p>¡Increíble experiencia! Gané el jackpot progresivo en Book of Dead y
                                                    recibí mi pago en menos de 2 horas. El soporte 24/7 resolvió todas mis
                                                    dudas inmediatamente. ¡Recomendado 100%!</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="testimonial-item">
                                        <div class="testimonial-inner">
                                            <div class="testimonial-head">
                                                <div class="testi-top">
                                                    <div class="testimonial-thumb">
                                                        <img src="assets/images/testimonial/02.jpg" alt="testimonio">
                                                    </div>
                                                    <div class="name-des">
                                                        <h5>Ana Rodríguez</h5>
                                                        <p>Fanática de los Torneos</p>
                                                    </div>
                                                </div>
                                                <div class="testimonial-footer">
                                                    <ul>
                                                        <li><i class="icofont-star"></i></li>
                                                        <li><i class="icofont-star"></i></li>
                                                        <li><i class="icofont-star"></i></li>
                                                        <li><i class="icofont-star"></i></li>
                                                        <li><i class="icofont-star"></i></li>
                                                    </ul>
                                                    <h5>5</h5>
                                                </div>
                                            </div>
                                            <div class="testimonial-body">
                                                <p>Los torneos de slots son adictivos y las bonificaciones de recarga son
                                                    generosas. Me encanta la variedad de juegos de proveedores como NetEnt y
                                                    Pragmatic Play. ¡Siempre hay promociones nuevas!</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ===========Sección Testimonios termina aquí========== -->
@endsection
