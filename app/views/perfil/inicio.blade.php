@include('includes.header')

<script src='{{ RUTA_URL }}/public/libs/fullcalendar-scheduler-6.1.17/dist/index.global.min.js'></script>
<link rel="stylesheet" type="text/css" href="{{ RUTA_URL }}/public/css/fullcalendar.css" />
<link rel="stylesheet" href="{{ RUTA_URL }}/public/libs/DataTables/datatables.min.css" />
<link rel="stylesheet" href="{{ RUTA_URL }}/public/libs/flatpickr.min.css">
<link rel="stylesheet" type="text/css" href="{{ RUTA_URL }}/public/libs/material_green.css">
<script src="{{ RUTA_URL }}/public/libs/flatpickr.min.js"></script>
<link rel="stylesheet" type="text/css" href="{{ RUTA_URL }}/public/css/styles/matapp-perfil.less">

@php
    $usuario = json_decode($_SESSION['userLogin']['usuario']);
    $loginUsuario = $usuario->login;
    $rolUsuario = $usuario->rol;
@endphp


<div style="height: 100vh;">
    <div class="container ">
        <div class="row">
            <div class="col-12 mb-4">
                <div class="profile-header position-relative mb-4"
                    style="
                    @if (!isset($usuario->imagen) || empty($usuario->imagen)) background: linear-gradient(135deg, #2847d1 0%, #e0dbdb 100%);
                    @else
                    background: url('data:image/jpeg;base64,{{ $usuario->imagen }}') no-repeat center center; background-size: cover; @endif
                ">
                </div>

                <div class="text-center">
                    <div class="position-relative d-inline-block">
                        <img src="@if (!isset($usuario->imagen) || empty($usuario->imagen)) {{ RUTA_URL . '/public/img/default_profile.png' }}
                        @else
                            data:image/jpeg;base64,{{ $usuario->imagen }} @endif"
                            class="rounded-circle profile-pic" alt="Profile Picture" />

                        <form action="{{ RUTA_URL }}/perfil/actualizarImagen" method="POST"
                            enctype="multipart/form-data" class="position-absolute bottom-0 end-0" id="formImagen">
                            <input type="file" name="imagen" id="imagen" class="d-none"
                                onchange="this.form.submit();">
                            <input type="hidden" name="id" value="{{ $usuario->idUsuario }}">
                            <label for="imagen" class="btn btn-primary btn-sm rounded-circle">
                                <i class="fas fa-camera"></i>
                            </label>
                        </form>

                    </div>
                    @if (isset($errores['imagen_error']))
                        <span id="error_imagen" class="small text-danger d-flex align-items-center"><svg
                                xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="-2 -3 24 24"
                                class="ms-1 me-1">
                                <path fill="currentColor"
                                    d="m12.8 1.613l6.701 11.161c.963 1.603.49 3.712-1.057 4.71a3.2 3.2 0 0 1-1.743.516H3.298C1.477 18 0 16.47 0 14.581c0-.639.173-1.264.498-1.807L7.2 1.613C8.162.01 10.196-.481 11.743.517c.428.276.79.651 1.057 1.096M10 14a1 1 0 1 0 0-2a1 1 0 0 0 0 2m0-9a1 1 0 0 0-1 1v4a1 1 0 0 0 2 0V6a1 1 0 0 0-1-1" />
                            </svg>{{ $errores['imagen_error'] }}</span>
                    @endif

                    <h3 class="mt-3 mb-1">
                        <span class="fs-6 opacity-50">@</span>{{ $usuario->login }}
                    </h3>

                </div>
            </div>

            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="row g-0">
                            <!-- Sidebar -->
                            <div class="col-lg-3 border-end">
                                <div class="p-4">
                                    <div class="nav flex-column nav-pills" id="perfilNav">
                                        <a class="nav-link active" href="#" data-section="infoPersonal"><i
                                                class="fas fa-user me-2"></i>Información personal</a>
                                        @if ($rolUsuario != 'Administrador')
                                            <a class="nav-link" href="#" data-section="infoClases"><i
                                                    class="fas fa-calendar-alt me-2"></i>Clases</a>

                                            <a class="nav-link" href="#" data-section="solicitudesS"><i
                                                    class="fas fa-envelope me-2"></i>Solicitudes</a>

                                            <a class="nav-link" href="#" data-section="asistencia"><i
                                                    class="fas fa-check-circle me-2"></i>Asistencia</a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-9">
                                <div class="p-4">
                                    <!-- Informacion personal  -->
                                    <div class="mb-4 content-section" id="infoPersonal">
                                        <h5 class="mb-4">Información personal</h5>
                                        <form id="editForm" action="{{ RUTA_URL }}/perfil/actualizarPerfil"
                                            method="POST">
                                            <input type="hidden" name="id" value="{{ $usuario->idUsuario }}">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">login de usuario</label>
                                                    <input type="text" class="form-control editable"
                                                        value="{{ $usuario->login }}" name="login" />
                                                    @if (isset($errores['login_error']))
                                                        <span class="small text-danger d-flex align-items-center">
                                                            {{ $errores['login_error'] }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" class="form-control editable"
                                                        value="{{ $usuario->emailUsuario }}" name="email" />
                                                    @if (isset($errores['email_error']))
                                                        ?>
                                                        <span class="small text-danger d-flex align-items-center">
                                                            {{ $errores['email_error'] }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Nombre</label>
                                                    <input type="text" class="form-control editable"
                                                        value="{{ $usuario->nombreUsuario }}" name="nombreUsuario" />
                                                    @if (isset($errores['nombreUsuario_error']))
                                                        <span class="small text-danger d-flex align-items-center">
                                                            {{ $errores['nombreUsuario_error'] }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Primer Apellido</label>
                                                    <input type="text" class="form-control editable"
                                                        value="{{ $usuario->apellido1Usuario }}"
                                                        name="apellido1Usuario" />
                                                    @if (isset($errores['apellido1Usuario_error']))
                                                        <span class="small text-danger d-flex align-items-center">
                                                            {{ $errores['apellido1Usuario_error'] }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Segundo Apellido</label>
                                                    <input type="text" class="form-control editable"
                                                        value="{{ $usuario->apellido2Usuario }}"
                                                        name="apellido2Usuario" />
                                                    @if (isset($errores['apellido2Usuario_error']))
                                                        <span class="small text-danger d-flex align-items-center">
                                                            {{ $errores['apellido2Usuario_error'] }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Teléfono</label>
                                                    <input type="text" class="form-control editable"
                                                        value="{{ $usuario->telefonoUsuario }}"
                                                        name="telefonoUsuario" />
                                                    @if (isset($errores['telefono_error']))
                                                        <span class="small text-danger d-flex align-items-center">
                                                            {{ $errores['telefono_error'] }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label">Nueva contraseña</label>
                                                    <input type="password" class="form-control editable"
                                                        name="password" />
                                                    @if (isset($errores['password_error']))
                                                        <span class="small text-danger d-flex align-items-center">
                                                            {{ $errores['password_error'] }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <button type="submit" id="saveButton" class="btn d-none">Actualizar
                                                    perfil</button>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- Tab de Clases -->
                                    <div class="mb-4 content-section d-none" id="infoClases">
                                        <h5 class="mb-4">Tus clases</h5>
                                        <div id="calendar-container" style="min-height: 500px;">
                                            <div id="calendar"></div>
                                        </div>
                                    </div>

                                    <div class="mb-4 content-section d-none" id="solicitudesS">
                                        <h5 class="mb-4">Tus solicitudes</h5>
                                        @if (isset($solicitudesS) && count($solicitudesS) > 0)
                                            <div class="table-responsive" id="solicitudesTableWrapper">
                                                <table id="solicitudesTable"
                                                    class="table table-striped table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Fecha</th>
                                                            <th>Academia</th>
                                                            <th>Estado</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($solicitudesS as $i => $solicitud)
                                                            <tr>
                                                                <td>{{ $solicitud->fechaSolicitud ?? '-' }}</td>
                                                                <td>
                                                                    @if (!empty($solicitud->path_imagen))
                                                                        <img src="{{ RUTA_IMG_ACADEMIAS . $solicitud->path_imagen }}"
                                                                            alt="Academia"
                                                                            style="width:24px;height:24px;object-fit:cover;border-radius:50%;margin-right:6px;">
                                                                    @else
                                                                        <img src="{{ RUTA_URL }}/public/img/favicon/favicon-32x32.png"
                                                                            alt="Academia"
                                                                            style="width:24px;height:24px;object-fit:cover;border-radius:50%;margin-right:6px;">
                                                                    @endif
                                                                    {{ $solicitud->nombreAcademia ?? '-' }}
                                                                </td>
                                                                <td>
                                                                    @if (isset($solicitud->estadoSolicitud))
                                                                        @if ($solicitud->estadoSolicitud == 'pendiente')
                                                                            <span
                                                                                class="badge bg-warning text-dark">Pendiente</span>
                                                                        @elseif($solicitud->estadoSolicitud == 'aceptada')
                                                                            <span
                                                                                class="badge bg-success">Aceptada</span>
                                                                        @elseif($solicitud->estadoSolicitud == 'rechazada')
                                                                            <span
                                                                                class="badge bg-danger">Rechazada</span>
                                                                        @else
                                                                            {{ $solicitud->estadoSolicitud }}
                                                                        @endif
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center">No tienes solicitudes registradas.</div>
                                        @endif
                                    </div>

                                    <div class="mb-4 content-section d-none" id="asistencia">
                                        <h5 class="mb-4">Clases a las que has asistido</h5>
                                        @if (isset($asistencias) && count($asistencias) > 0)
                                            <div class="table-responsive" id="asistenciaTableWrapper">
                                                <table id="asistenciaTable"
                                                    class="table table-striped table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Fecha</th>
                                                            <th>Academia</th>
                                                            <th>Clase</th>
                                                            <th>Valorar</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($asistencias as $asistencia)
                                                            <tr>
                                                                <td>{{ $asistencia->fecha ?? '-' }}</td>
                                                                <td>
                                                                    @if (!empty($asistencia->path_imagen))
                                                                        <img src="{{ RUTA_IMG_ACADEMIAS . $asistencia->path_imagen }}"
                                                                            alt="Academia"
                                                                            style="width:24px;height:24px;object-fit:cover;border-radius:50%;margin-right:6px;">
                                                                    @else
                                                                        <img src="{{ RUTA_URL }}/public/img/favicon/favicon-32x32.png"
                                                                            alt="Academia"
                                                                            style="width:24px;height:24px;object-fit:cover;border-radius:50%;margin-right:6px;">
                                                                    @endif
                                                                    {{ $asistencia->nombreAcademia ?? '-' }}
                                                                </td>
                                                                <td>{{ $asistencia->nombreClase ?? '-' }}</td>
                                                                <td>
                                                                    @if ($asistencia->valoracion !== null)
                                                                        @for ($i = 1; $i <= 5; $i++)
                                                                            @if ($i <= $asistencia->valoracion)
                                                                                <i class="fas fa-star text-warning"></i>
                                                                            @else
                                                                                <i class="far fa-star text-warning"></i>
                                                                            @endif
                                                                        @endfor
                                                                    @else
                                                                        <button class="btn btn-outline-primary btn-sm" onclick="valorar('{{ $asistencia->idClase }}', false)">
                                                                            Valorar
                                                                        </button>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center">No tienes asistencias registradas.</div>
                                        @endif
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


@if (isset($_GET['toastrMsg']))
    <script>
        let toastrMsg = `{{ $_GET['toastrMsg'] }}`;
    </script>
@endif

<script>
    const USUARIO_ID = `{{ json_encode($usuario->idUsuario) }}`;
</script>

<script type="module" src="{{ RUTA_URL }}/public/js/perfil.inicio.js"></script>
<script src="{{ RUTA_URL }}/public/libs/DataTables/datatables.min.js"></script>

@include('includes.footer')
