<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ NOMBRE_SITIO }}</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> {{-- Carga jQuery desde CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script> {{-- Carga bootstrap desde CDN --}}
    {{-- Carga bootstrap desde CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    {{-- Carga fontawesome desde CDN --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    {{-- Carga CSS genérico para el chatBot --}}
    <link rel="stylesheet" type="text/css" href="{{ RUTA_URL }}/public/css/chatbot.css" /> {{-- Carga css desde archivo local --}}
    <!-- Fuentes e íconos para chatbot-->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    {{-- Carga FULLCALENDAR desde su ruta en local --}}
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.17/index.global.min.js'></script>
    <link rel="stylesheet" type="text/css" href="{{ RUTA_URL }}/public/css/fullcalendar.css" />
    {{-- Carga sweetalerts desde archivo CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- Carga datatablesJS css desde CDN --}}
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"> --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> {{-- Carga chart.js desde CDN --}}
    {{-- Carga flatpickr desde CDN --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_green.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script> {{-- Carga flatpickr desde cdn --}}
    {{-- Carga offcanvasMenuLibrary --}}

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
    <div class="container mt-5" id="main">


        <nav>

            @php
                $usuarioLogueado = isset($_SESSION['userLogin']['usuario']);
                if ($usuarioLogueado) {
                    $usuario = json_decode($_SESSION['userLogin']['usuario']);
                    $loginUsuario = $usuario->login; // o el campo que uses como nombre
                    $rolUsuario = $usuario->rol; // o el campo que uses como nombre
                    
                } else {
                    $nombreUsuario = 'Invitado';
                }
            @endphp

            @if ($usuarioLogueado)
                <a href="{{ RUTA_URL }}/perfil/"><?= $loginUsuario ?></a>
                <span href="{{ RUTA_URL }}/perfil/"><?= $rolUsuario ?></a>
                <a href="{{ RUTA_URL }}/inicioSesion/cerrarSesion">Cerrar Sesión</a>
            @else
                <a href="{{ RUTA_URL }}/inicioSesion/">Iniciar Sesión</a>
            @endif

            <strong><a class="" href="{{ RUTA_URL }}/">Inicio</a></strong>

        </nav>
