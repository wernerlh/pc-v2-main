@extends('layouts.app')

@section('title', 'Sedes - casino')

@section('content')
    <!-- ===========Sección Banner comienza aquí========== -->
    <section class="pageheader-section" style="background-image: url({{ asset('assets/images/pageheader/bg.jpg') }});">
        <div class="container">
            <div class="section-wrapper text-center text-uppercase">
                <h2 class="pageheader-title">Nuestras Sedes</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                        <li class="breadcrumb-item active" aria-current="page">/ Sedes</li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>
    <!-- ===========Sección Banner termina aquí========== -->

    <!-- ===========Sección Sedes comienza aquí========== -->
    <div class="partner-section padding-top padding-bottom">
        <div class="container">
            <div class="section-header">
                <h2>Encuentra tu sede más cercana</h2>
                <p>Visita cualquiera de nuestras sedes y disfruta de la mejor experiencia de juego en todo el país</p>
            </div>
            <div class="section-wrapper">
                <div class="row g-4">
                    <div class="col-lg-12 col-12">
                        <div class="partner-list" id="sedesAccordion">
                            <div class="row g-4 justify-content-center">
                                <div class="col-12">
                                    <div class="accordion-item">
                                        <div class="accordion-header" id="headingSede1">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapseSede1"
                                                aria-expanded="true" aria-controls="collapseSede1">
                                                <span class="accor-header-inner d-flex flex-wrap align-items-center">
                                                    <span class="accor-title">Sede Central - Lima</span>
                                                </span>
                                            </button>
                                        </div>
                                        <div id="collapseSede1" class="accordion-collapse collapse"
                                            aria-labelledby="headingSede1" data-bs-parent="#sedesAccordion">
                                            <div class="accordion-body">
                                                <div class="row">

                                                    <div class="col-md-8">
                                                        <p><i class="icofont-location-pin"></i> Av. Javier Prado Este 1234,
                                                            San Isidro</p>
                                                        <p><i class="icofont-phone"></i> (01) 555-7890</p>
                                                        <p><i class="icofont-clock-time"></i> Lunes a Domingo: 10:00 AM -
                                                            4:00 AM</p>

                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="accordion-item">
                                        <div class="accordion-header" id="headingSede2">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapseSede2"
                                                aria-expanded="false" aria-controls="collapseSede2">
                                                <span class="accor-header-inner d-flex flex-wrap align-items-center">
                                                    <span class="accor-title">Sede Miraflores</span>
                                                </span>
                                            </button>
                                        </div>
                                        <div id="collapseSede2" class="accordion-collapse collapse"
                                            aria-labelledby="headingSede2" data-bs-parent="#sedesAccordion">
                                            <div class="accordion-body">
                                                <div class="row">

                                                    <div class="col-md-8">

                                                        <p><i class="icofont-location-pin"></i> Av. Larco 789, Miraflores
                                                        </p>
                                                        <p><i class="icofont-phone"></i> (01) 445-6789</p>
                                                        <p><i class="icofont-clock-time"></i> Lunes a Domingo: 12:00 PM -
                                                            3:00 AM</p>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="accordion-item">
                                        <div class="accordion-header" id="headingSede3">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapseSede3"
                                                aria-expanded="false" aria-controls="collapseSede3">
                                                <span class="accor-header-inner d-flex flex-wrap align-items-center">
                                                    <span class="accor-title">Sede Arequipa</span>
                                                </span>
                                            </button>
                                        </div>
                                        <div id="collapseSede3" class="accordion-collapse collapse"
                                            aria-labelledby="headingSede3" data-bs-parent="#sedesAccordion">
                                            <div class="accordion-body">
                                                <div class="row">

                                                    <div class="col-md-8">

                                                        <p><i class="icofont-location-pin"></i> Av. Cayma 456, Arequipa</p>
                                                        <p><i class="icofont-phone"></i> (054) 123-4567</p>
                                                        <p><i class="icofont-clock-time"></i> Lunes a Domingo: 11:00 AM -
                                                            2:00 AM</p>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="accordion-item">
                                        <div class="accordion-header" id="headingSede4">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapseSede4"
                                                aria-expanded="false" aria-controls="collapseSede4">
                                                <span class="accor-header-inner d-flex flex-wrap align-items-center">
                                                    <span class="accor-title">Sede Trujillo</span>
                                                </span>
                                            </button>
                                        </div>
                                        <div id="collapseSede4" class="accordion-collapse collapse"
                                            aria-labelledby="headingSede4" data-bs-parent="#sedesAccordion">
                                            <div class="accordion-body">
                                                <div class="row">

                                                    <div class="col-md-8">

                                                        <p><i class="icofont-location-pin"></i> Av. Húsares de Junín 345,
                                                            Trujillo</p>
                                                        <p><i class="icofont-phone"></i> (044) 987-6543</p>
                                                        <p><i class="icofont-clock-time"></i> Lunes a Domingo: 10:30 AM -
                                                            3:30 AM</p>

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
        </div>
    </div>
    <!-- ===========Sección Sedes termina aquí========== -->

    <!-- ===========Sección Información Adicional comienza aquí========== -->
    <section class="partner-section padding-top padding-bottom">
        <div class="container">
            <div class="section-header">
                <h2>Información importante</h2>
            </div>
            <div class="row justify-content-center g-4">
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="info-item text-center">
                        <div class="info-icon">
                            <i class="icofont-clock-time"></i>
                        </div>
                        <div class="info-content">
                            <h5>Horarios de atención</h5>
                            <p>Todas nuestras sedes cuentan con horarios extendidos para que puedas disfrutar del mejor
                                entretenimiento cuando lo desees.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="info-item text-center">
                        <div class="info-icon">
                            <i class="icofont-safety"></i>
                        </div>
                        <div class="info-content">
                            <h5>Protocolos de seguridad</h5>
                            <p>Implementamos rigurosos protocolos de seguridad e higiene para garantizar tu tranquilidad y
                                diversión.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="info-item text-center">
                        <div class="info-icon">
                            <i class="icofont-star"></i>
                        </div>
                        <div class="info-content">
                            <h5>Beneficios exclusivos</h5>
                            <p>Cada sede cuenta con promociones y beneficios exclusivos. ¡Visita tu sede favorita y
                                descúbrelos!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ===========Sección Información Adicional termina aquí========== -->
@endsection
