<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ NOMBRE_SITIO }}</title>
    <link rel="icon" type="image/png" href="{{ RUTA_URL }}/public/img/favicon/favicon-32x32.png">
    <script src="{{ RUTA_URL }}/public/libs/jquery-3.7.1.min.js"></script>
    <script src="{{ RUTA_URL }}/public/libs/sweetalert2.all.js"></script>
    <script src="{{ RUTA_URL }}/public/libs/bootstrap-5.3.5-dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>
    <link href="{{ RUTA_URL }}/public/libs/bootstrap-5.3.5-dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
    <link rel="stylesheet" href="{{ RUTA_URL }}/public/libs/fontawesome-free-6.7.2-web/css/all.min.css" />
    <link rel="stylesheet" href="{{ RUTA_URL }}/public/libs/toastr.min.css">
    <script src="{{ RUTA_URL }}/public/libs/toastr.min.js"></script>
    <link rel="stylesheet/less" href="{{ RUTA_URL }}/public/css/styles/matapp.less" type="text/css" />
    <link rel="stylesheet" href="https://use.typekit.net/nxn8pcg.css">
</head>

<body>

    <div class="container" id="main">


        <nav id="navegacion" class="navbar mb-3 mt-3">
            <div class="container-fluid d-flex justify-content-end">

                @if (!isset($_SESSION['userLogin']))
                    <span class="text-white me-auto">
                        <img src="{{ RUTA_URL }}/public/img/favicon/android-chrome-512x512.png" alt="Logo"
                            class="img-fluid" width="40" height="40" id="logo" title="Home" />
                    </span>

                    <a class="navbar-brand text-black me-0 d-flex align-items-center" id="iniciar-sesion"
                        title="Iniciar Sesion" href="{{ RUTA_URL }}/inicioSesion">
                        <span class="small">Iniciar Sesion</span>
                    </a>
                    <a id="registrarse" title="Registrarse" class="btn btn-dark ms-3" type="submit"
                        href="{{ RUTA_URL }}/registroUsuario">
                        Registrarse
                    </a>
                @else
                    @php
                        $usuario = json_decode($_SESSION['userLogin']['usuario']);
                    @endphp

                    <span class="text-white me-auto">
                        <img src="{{ RUTA_URL }}/public/img/favicon/android-chrome-512x512.png" alt="Logo"
                            class="img-fluid" width="40" height="40" id="logo" title="Home" />
                        @if (isset($usuario->rol) && $usuario->rol == 'Administrador')
                            <span class="text-danger small p-1">
                                <i class="fa-solid fa-lock me-1"></i>Administrador
                            </span>
                        @endif
                    </span>


                    <div class="dropdown me-3">
                        <button class="btn btn-light position-relative" type="button" id="dropdownNotificaciones"
                            data-bs-toggle="dropdown" aria-expanded="false" title="Notificaciones">
                            <i class="fa fa-bell"></i>
                            <span
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill"
                                id="notificaciones-count">
                                0
                                <span class="visually-hidden">mensajes nuevos</span>
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownNotificaciones"
                            style="min-width: 300px;" id="notificaciones-list">
                            <li class="dropdown-header">Notificaciones</li>
                            <li class="text-center py-2" id="notificaciones-loader">
                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                            </li>
                        </ul>
                    </div>


                    <a href="#" id="amigos" class="btn btn-light me-3 position-relative" title="Amigos"
                        data-bs-toggle="offcanvas" data-bs-target="#offcanvasAmigos">
                        <i class="fa-solid fa-user-group text-white"></i>
                        <span id="badgeSolicitudesBtn"
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            style="display:none;">0</span>
                    </a>

                    <button id="botonPerfil" class="btn rounded-circle p-0" type="button" data-bs-toggle="offcanvas"
                        title="Menú" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                        @if (!empty($usuario->imagen))
                            <img src="data:image/jpeg;base64,{{ $usuario->imagen }}" alt="Imagen del cliente"
                                class="rounded-circle" style="width: 40px; height: 40px;">
                        @else
                            <img src="{{ RUTA_URL }}/public/img/default_profile.png" alt="Imagen por defecto"
                                class="rounded-circle" style="width: 40px; height: 40px;">
                        @endif
                    </button>

                    <div class="offcanvas offcanvas-end  text-white" tabindex="-1" id="offcanvasRight"
                        aria-labelledby="offcanvasRightLabel">
                        <div class="offcanvas-header">
                            @if (!empty($usuario->imagen))
                                <img src="data:image/jpeg;base64,{{ $usuario->imagen }}" alt="Imagen del cliente"
                                    class="me-2 rounded-circle" style="width: 40px; height: 40px;">
                            @else
                                <img src="{{ RUTA_URL }}/public/img/default_profile.png"
                                    alt="Imagen por defecto" class="me-2 rounded-circle"
                                    style="width: 40px; height: 40px;">
                            @endif
                            <h5 class="offcanvas-title text-dark" id="offcanvasRightLabel">
                                {{ isset($usuario->login) ? $usuario->login : 'Invitado' }}
                            </h5>
                            <button type="button" class="cerrarBoton btn-close btn-close-dark"
                                data-bs-dismiss="offcanvas" aria-label="Close" title="Cerrar"></button>
                        </div>
                        <div class="offcanvas-body">
                            {{-- TODO noticiero --}}
                            <div class="row">

                                @if (isset($usuario->rol) && $usuario->rol == 'Administrador')
                                    <div class="col-12 mb-1">
                                        <a class="menu-item w-100 btn btn-light text-start text-dark text-opacity-70"
                                            href="{{ RUTA_URL }}/admin"><i
                                                class="fa-solid fa-lock"></i>&nbsp;&nbsp;Panel
                                            de administración</a>
                                    </div>
                                @endif
                                <div class="col-12 mb-2">
                                    <a class="menu-item w-100 btn btn-light text-start text-dark text-opacity-70"
                                        href="{{ RUTA_URL }}/perfil"><i class="fa fa-user"></i>&nbsp;&nbsp;Tu
                                        perfil </a>
                                </div>
                                <div class="col-12 mb-1">
                                    <a href="#"
                                        class="menu-item w-100 btn btn-light text-start text-dark text-opacity-70"
                                        data-bs-toggle="offcanvas" data-bs-target="#offcanvasAmigos">
                                        <i class="fa-solid fa-user-group"></i>&nbsp;&nbsp;Tus amigos
                                    </a>
                                </div>
                                <div class="col-12 mt-2 border-top border-dark-subtle">
                                    <a class=" link link-danger d-flex align-items-center text-decoration-none ms-2 mt-3"
                                        href="{{ RUTA_URL }}/inicioSesion/cerrarSesion"><i
                                            class="fa-solid fa-right-from-bracket"></i>&nbsp;Cerrar Sesion </a>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAmigos"
                        aria-labelledby="offcanvasAmigosLabel">
                        <div class="offcanvas-header">
                            <h5 class="offcanvas-title" id="offcanvasAmigosLabel">Amigos</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                                aria-label="Cerrar"></button>
                        </div>
                        <div class="offcanvas-body">
                            <ul class="nav nav-tabs" id="amigosTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="buscar-tab" data-bs-toggle="tab"
                                        data-bs-target="#buscar" type="button" role="tab">Buscar
                                        usuarios</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="amistades-tab" data-bs-toggle="tab"
                                        data-bs-target="#amistades" type="button" role="tab">Mis
                                        amistades</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="solicitudes-tab" data-bs-toggle="tab"
                                        data-bs-target="#solicitudes" type="button" role="tab">
                                        Solicitudes
                                        <span id="badgeSolicitudes" class="badge bg-danger ms-1"
                                            style="display:none;">0</span>
                                    </button>
                                </li>
                            </ul>
                            <div class="tab-content mt-3" id="amigosTabsContent">
                                <div class="tab-pane fade show active" id="buscar" role="tabpanel">
                                    <input type="text" class="form-control mb-2" id="buscarUsuarioInput"
                                        placeholder="Buscar usuario...">
                                    <ul class="list-group" id="resultadosBusqueda"></ul>
                                </div>
                                <div class="tab-pane fade" id="amistades" role="tabpanel">
                                    <ul class="list-group" id="listaAmigos">
                                        <li class="list-group-item text-muted">Cargando...</li>
                                    </ul>
                                </div>
                                <div class="tab-pane fade" id="solicitudes" role="tabpanel">
                                    <ul class="list-group" id="listaSolicitudes">
                                        <li class="list-group-item text-muted">Cargando...</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </nav>



        <div class="modal fade" id="modalPerfilAmigo" tabindex="-1" aria-labelledby="modalPerfilAmigoLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalPerfilAmigoLabel">Perfil del Amigo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body" id="perfilAmigoContenido">
                    </div>
                </div>
            </div>
        </div>


        <script type="module" src="{{ RUTA_URL }}/public/js/header.logo.js"></script>
        <script type="module" src="{{ RUTA_URL }}/public/js/header.notificaciones.js"></script>
        <script type="module" src="{{ RUTA_URL }}/public/js/header.amigos.js"></script>

</body>

</html>
