<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ NOMBRE_SITIO }}</title>
    <link rel="icon" type="image/png" href="{{ RUTA_URL }}/public/img/favicon/favicon-32x32.png">
    <script src="{{ RUTA_URL }}/public/libs/jquery-3.7.1.min.js"></script> {{-- Carga jQuery  --}}
    <script src="{{ RUTA_URL }}/public/libs/bootstrap-5.3.5-dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script> {{-- Carga bootstrap  --}}
    {{-- Carga bootstrap  --}}
    <link href="{{ RUTA_URL }}/public/libs/bootstrap-5.3.5-dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
    {{-- Carga fontawesome desde CDN --}}
    <link rel="stylesheet" href="{{ RUTA_URL }}/public/libs/fontawesome-free-6.7.2-web/css/all.min.css" />
    {{-- Carga CSS genérico para el chatBot --}}
    <link rel="stylesheet" type="text/css" href="{{ RUTA_URL }}/public/css/chatbot.css" /> {{-- Carga css desde archivo local --}}
    <!-- Fuentes e íconos para chatbot-->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    {{-- Carga FULLCALENDAR desde su ruta en local --}}
    <script src='{{ RUTA_URL }}/public/libs/fullcalendar-scheduler-6.1.17/dist/index.global.min.js'></script>
    <link rel="stylesheet" type="text/css" href="{{ RUTA_URL }}/public/css/fullcalendar.css" />
    {{-- Carga datatablesJS css desde CDN --}}
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"> --}}
    <link rel="stylesheet" href="{{ RUTA_URL }}/public/libs/DataTables/datatables.min.css" />
    {{-- Carga datatables desde archivo local --}}
    {{-- Carga flatpickr desde CDN --}}
    <link rel="stylesheet" href="{{ RUTA_URL }}/public/libs/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="{{ RUTA_URL }}/public/libs/material_green.css">
    <script src="{{ RUTA_URL }}/public/libs/flatpickr.min.js"></script> {{-- Carga flatpickr desde cdn --}}
    <script src="{{ RUTA_URL }}/public/libs/chart.umd.js"></script> {{-- Carga ChartJs  --}}
    {{-- Carga sweetalerts desde archivo CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

</head>

<body>

    {{-- google translate --}}
    {{-- <div id="google_translate_element"></div>
    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'es',
                includedLanguages: 'en,es,fr,de,it,pt', // Lista de idiomas disponibles
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE // Diseño más moderno y compacto
            }, 'google_translate_element');
        }
    </script>
    <script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit">
    </script>

    <div class="container mt-5" id="main"> --}}


    {{-- start chatbot --}}
    {{-- <button class="chatbot-toggler">
            <span class="material-symbols-outlined">mode_comment</span>
            <span class="material-symbols-outlined">close</span>
        </button>

        <div class="chatbot">
            <header>
                <h2>ChatBot - MatApp</h2>
                <span class="close-btn material-symbols-outlined">close</span>
            </header>
            <ul class="chatbox" id="chatbox">
                <li class="chat incoming">
                    <span class="material-symbols-outlined">smart_toy</span>
                    <p>Hola 👋🏻<br />¿Cómo puedo ayudarte hoy?</p>
                </li>
            </ul>
            <div class="chat-input">
                <textarea placeholder="Ingresa un mensaje..." required id="user-input"></textarea>
                <span id="send-btn" class="material-symbols-outlined">send</span>
            </div>
        </div> --}}
    {{-- stop chatbot --}}


    {{-- se repitita  --}}
    <div class="container" id="main">

        <link rel="stylesheet" type="text/css" href="{{ RUTA_URL }}/public/css/header.css" />

        <nav id="navegacion" class="navbar mb-3 mt-3">
            <div class="container-fluid d-flex justify-content-end">

                <?php
        
                if (!isset($_SESSION['userLogin'])) {
        
                ?>
                <span class="text-white me-auto">
                    <img src="<?= RUTA_URL ?>/public/img/favicon/android-chrome-512x512.png" alt="Logo"
                        class="img-fluid" width="40" height="40" id="logo" title="Home" />
                </span>


                <a class="navbar-brand text-black me-0 d-flex align-items-center" id="iniciar-sesion"
                    href="<?= RUTA_URL ?>/inicioSesion">
                    <!-- Esta el svg dentro del codigo para poder hacerle el fill -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                        <path fill="currentColor"
                            d="M12 3.75a3.75 3.75 0 1 0 0 7.5a3.75 3.75 0 0 0 0-7.5m-4 9.5A3.75 3.75 0 0 0 4.25 17v1.188c0 .754.546 1.396 1.29 1.517c4.278.699 8.642.699 12.92 0a1.54 1.54 0 0 0 1.29-1.517V17A3.75 3.75 0 0 0 16 13.25h-.34q-.28.001-.544.086l-.866.283a7.25 7.25 0 0 1-4.5 0l-.866-.283a1.8 1.8 0 0 0-.543-.086z" />
                    </svg>
                    <span class="small">Iniciar Sesion</span>
                </a>
                <a id="registrarse" class="btn btn-dark ms-3" type="submit" href="<?= RUTA_URL ?>/registroUsuario">
                    Registrarse
                </a>

                <?php
                } else {

                    $usuario = json_decode($_SESSION['userLogin']['usuario']);

                ?>

                <span class="text-white me-auto">
                    <img src="<?= RUTA_URL ?>/public/img/favicon/android-chrome-512x512.png" alt="Logo"
                        class="img-fluid" width="40" height="40" id="logo" title="Home" />
                </span>

                <button id="botonPerfil" class="btn rounded-circle p-0" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                    <?php if (!empty($usuario->pathImagen)) { ?>
                    <img src="#" alt="Imagen del cliente" class="rounded-circle"
                        style="width: 40px; height: 40px;">
                    <?php } else { ?>
                    <img src="<?= RUTA_URL ?>/public/img/default_profile.png" alt="Imagen por defecto"
                        class=" rounded-circle" style="width: 40px; height: 40px;">
                    <?php } ?>
                </button>

                <div class="offcanvas offcanvas-end  text-white" tabindex="-1" id="offcanvasRight"
                    aria-labelledby="offcanvasRightLabel">
                    <div class="offcanvas-header">
                        <?php if (isset($usuario->pathImagen)) { ?>
                        <img src="#" alt="Imagen del cliente" class="me-2 rounded-circle"
                            style="width: 40px; height: 40px;">
                        <?php } else { ?>
                        <img src="<?= RUTA_URL ?>/public/img/default_profile.png" alt="Imagen por defecto"
                            class="me-2 rounded-circle" style="width: 40px; height: 40px;">
                        <?php } ?>
                        <h5 class="offcanvas-title text-dark" id="offcanvasRightLabel">
                            <?= isset($usuario->login) ? $usuario->login : 'Invitado' ?>
                        </h5>
                        <button type="button" class="cerrarBoton btn-close btn-close-dark" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">

                        <div class="row">
                            
                            <?php if (isset($usuario->rol) && $usuario->rol == "Administrador") { ?>
                            <div class="col-12 mb-1">
                                <a class="menu-item w-100 btn btn-light text-start text-dark text-opacity-70"
                                    href="<?= RUTA_URL ?>/admin"><i class="fa-solid fa-lock"></i>&nbsp;&nbsp;Panel
                                    de administración</a>
                            </div>
                            <?php }else{ ?>
                            <div class="col-12 mb-1">
                                <a class="menu-item w-100 btn btn-light text-start text-dark text-opacity-70"
                                    href="<?= RUTA_URL ?>/perfil"><i class="fa-regular fa-user"></i>&nbsp;&nbsp;Tu
                                    perfil </a>
                            </div>
                            <?php } ?>  
                            <div class="col-12 mt-2 border-top border-dark-subtle">
                                <a class=" link link-danger d-flex align-items-center text-decoration-none ms-2 mt-3"
                                    href="<?= RUTA_URL ?>/inicioSesion/cerrarSesion"><i
                                        class="fa-solid fa-right-from-bracket"></i>&nbsp;Cerrar Sesion </a>
                            </div>
                        </div>

                    </div>
                </div>

                <?php
                }
                ?>



            </div>
        </nav>



        <script>
            document.getElementById('logo').addEventListener('click', function() {
                window.location.href = '<?= RUTA_URL ?>'; // Cambia esta URL por la URL de destino
            });
        </script>
