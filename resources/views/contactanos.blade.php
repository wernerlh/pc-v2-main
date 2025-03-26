@extends('layouts.app')

@section('title', 'Contacto - casino')

@section('content')
    <!-- ===========Sección Banner comienza aquí========== -->
    <section class="pageheader-section" style="background-image: url({{ asset('assets/images/pageheader/bg.jpg') }});">
        <div class="container">
            <div class="section-wrapper text-center text-uppercase">
                <h2 class="pageheader-title">Contacto</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                        <li class="breadcrumb-item active" aria-current="page">/ Contacto</li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>
    <!-- ===========Sección Banner termina aquí========== -->

    <!-- ===========Sección Información de Contacto comienza aquí========== -->
    <section class="contact-section padding-top padding-bottom">
        <div class="container">
            <div class="section-header">
                <h2>¿Cómo podemos ayudarte?</h2>
                <p>Estamos disponibles para atender todas tus consultas y brindarte la mejor experiencia</p>
            </div>

            <div class="section-wrapper">
                <div class="row justify-content-center g-4">
                    <!-- Tarjeta de Contacto Principal -->
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="contact-item text-center">
                            <div class="contact-icon">
                                <i class="icofont-phone"></i>
                            </div>
                            <div class="contact-content">
                                <h4>Líneas Telefónicas</h4>
                                <p>Atención al Cliente: (01) 555-7890</p>
                                <p>Sala VIP: (01) 555-7891</p>
                                <p>Eventos: (01) 555-7892</p>
                                <p>WhatsApp: +51 987 654 321</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta de Correos Electrónicos -->
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="contact-item text-center">
                            <div class="contact-icon">
                                <i class="icofont-email"></i>
                            </div>
                            <div class="contact-content">
                                <h4>Correo Electrónico</h4>
                                <p>Información General: <a href="mailto:info@casino.com">info@casino.com</a></p>
                                <p>Servicio al Cliente: <a href="mailto:atencion@casino.com">atencion@casino.com</a></p>
                                <p>Departamento VIP: <a href="mailto:vip@casino.com">vip@casino.com</a></p>
                                <p>Eventos y Promociones: <a href="mailto:eventos@casino.com">eventos@casino.com</a></p>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta de Sede Principal -->
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="contact-item text-center">
                            <div class="contact-icon">
                                <i class="icofont-location-pin"></i>
                            </div>
                            <div class="contact-content">
                                <h4>Sede Principal</h4>
                                <p>Av. Javier Prado Este 1234, San Isidro</p>
                                <p>Lima, Perú</p>
                                <p>Referencia: Frente al Centro Comercial Real Plaza</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Horarios de Atención -->
                <div class="row mt-5">
                    <div class="col-12">
                        <div class="horario-container">
                            <div class="section-header">
                                <h3>Horarios de Atención</h3>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-lg-8">
                                    <div class="horarios-table">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th>Día</th>
                                                        <th>Casino</th>
                                                        <th>Restaurante</th>
                                                        <th>Bar VIP</th>
                                                    </tr>
                                                </thead>
                                                <tbody style="background-color: #f8f9fa;">
                                                    <tr>
                                                        <td>Lunes a Jueves</td>
                                                        <td>10:00 AM - 4:00 AM</td>
                                                        <td>12:00 PM - 12:00 AM</td>
                                                        <td>6:00 PM - 3:00 AM</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Viernes</td>
                                                        <td>10:00 AM - 5:00 AM</td>
                                                        <td>12:00 PM - 1:00 AM</td>
                                                        <td>6:00 PM - 4:00 AM</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Sábado</td>
                                                        <td>10:00 AM - 5:00 AM</td>
                                                        <td>12:00 PM - 1:00 AM</td>
                                                        <td>6:00 PM - 4:00 AM</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Domingo</td>
                                                        <td>12:00 PM - 2:00 AM</td>
                                                        <td>12:00 PM - 10:00 PM</td>
                                                        <td>4:00 PM - 12:00 AM</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Feriados</td>
                                                        <td>12:00 PM - 4:00 AM</td>
                                                        <td>12:00 PM - 12:00 AM</td>
                                                        <td>4:00 PM - 3:00 AM</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <p class="text-center mt-3"><small>*Horarios sujetos a cambios durante eventos especiales</small></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ===========Sección Información de Contacto termina aquí========== -->
@endsection