@extends('layouts.app')

@section('title', 'Membresías - casino')

@section('content')
    <!-- ===========Sección Banner comienza aquí========== -->
    <section class="pageheader-section" style="background-image: url({{ asset('assets/images/pageheader/bg.jpg') }});">
        <div class="container">
            <div class="section-wrapper text-center text-uppercase">
                <h2 class="pageheader-title">Nuestras Membresías</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                        <li class="breadcrumb-item active" aria-current="page">/ Membresías</li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>
    <!-- ===========Sección Banner termina aquí========== -->

    <!-- ===========Sección Membresías comienza aquí========== -->
    <div class="partner-section padding-top padding-bottom">
        <div class="container">
            <div class="section-wrapper">
                <div class="row g-4">
                    <div class="col-lg-12 col-12">
                        <!-- Añade un mensaje de depuración -->
                        @if (isset($membresias))
                            <p>Total de membresías: {{ $membresias->count() }}</p>
                        @else
                            <p>Variable $membresias no definida</p>
                        @endif

                        <div class="partner-list" id="accordionExample">
                            <div class="row g-4 justify-content-center">
                                @if (isset($membresias) && count($membresias) > 0)
                                    @foreach ($membresias as $membresia)
                                        <div class="col-12">
                                            <div class="accordion-item">
                                                <div class="accordion-header" id="heading{{ $membresia->id }}">
                                                    <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#collapse{{ $membresia->id }}"
                                                        aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                                        aria-controls="collapse{{ $membresia->id }}">
                                                        <span
                                                            class="accor-header-inner d-flex flex-wrap align-items-center">
                                                            <span class="accor-title">{{ $membresia->nombre }}</span>

                                                        </span>
                                                    </button>
                                                </div>
                                                <div id="collapse{{ $membresia->id }}"
                                                    class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                                    aria-labelledby="heading{{ $membresia->id }}"
                                                    data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-md-8">
                                                                <h4 class="mb-4" style="color: black;">{{ $membresia->nombre }}</h4>
                                                                <span
                                                                    class="ms-auto badge bg-primary rounded-pill mb-4">{{ number_format($membresia->precio, 2) }}
                                                                    $</span>
                                                                <h5 class="mb-4" style="color: black;">Descripcion:</h5>
                                                                <p class="mb-4">
                                                                    {{ $membresia->descripcion ?? 'Sin descripción' }}</p>

                                                                <h5 class="mb-4" style="color: black;">Beneficios:</h5>
                                                                <div class="benefits-list" style="color: black;">
                                                                    {!! $membresia->beneficios ?? 'Sin beneficios especificados' !!}
                                                                </div>
                                                                <h5 class="mb-4" style="color: black;">Descuento:</h5>
                                                                <p class="mb-4">
                                                                    {!! $membresia->descuento_porcentaje ?? 'Sin descuento especificado' !!} %
                                                                </p>

                                                                <div class="mt-4">
                                                                    <a href="/usuariocasino/comprar-membresia"
                                                                        class="default-button">
                                                                        <span>Adquirir ahora <i
                                                                                class="icofont-check-circled"></i></span>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-12 text-center">
                                        <div class="alert alert-info">
                                            <h5>No hay membresías disponibles en este momento.</h5>
                                            <p>Por favor, intenta más tarde o contacta con soporte.</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ===========Sección Membresías termina aquí========== -->



@endsection
