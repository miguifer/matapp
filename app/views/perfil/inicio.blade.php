@include('includes.header')

<?php if (isset($_GET['toastrMsg'])) {?>

<script>
    toastr.options = {
        "closeButton": true,
        "positionClass": "toast-top-right",
        "timeOut": "10000",
        "progressBar": true,
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut",
    };

    toastr.info('<?= $_GET['toastrMsg'] ?>');
</script>

<?php } ?>

<link rel="stylesheet" type="text/css" href="<?= RUTA_URL ?>/public/css/perfil.css">


<?php
$usuario = json_decode($_SESSION['userLogin']['usuario']);

if ($usuario->rol == 'Administrador') {
    redireccionar('/admin');
}

?>
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
                    <?php
                    if (!isset($usuario->imagen) || empty($usuario->imagen)) {
                        echo 'background: linear-gradient(135deg, #2847d1 0%, #e0dbdb 100%);';
                    } else {
                        echo 'background: url(\'data:image/jpeg;base64,' . $usuario->imagen . '\') no-repeat center center; background-size: cover;';
                    }
                    ?>
                ">
                </div>

                <div class="text-center">
                    <div class="position-relative d-inline-block">
                        <img src="<?php
                        if (!isset($usuario->imagen) || empty($usuario->imagen)) {
                            echo RUTA_URL . '/public/img/default_profile.png';
                        } else {
                            echo 'data:image/jpeg;base64,' . $usuario->imagen;
                        }
                        ?>" class="rounded-circle profile-pic" alt="Profile Picture" />

                        <form action="<?= RUTA_URL ?>/perfil/actualizarImagen" method="POST"
                            enctype="multipart/form-data" class="position-absolute bottom-0 end-0" id="formImagen">
                            <input type="file" name="imagen" id="imagen" class="d-none"
                                onchange="this.form.submit();">
                            <input type="hidden" name="id" value="<?= $usuario->idUsuario ?>">
                            <label for="imagen" class="btn btn-primary btn-sm rounded-circle">
                                <i class="fas fa-camera"></i>
                            </label>
                        </form>

                    </div>
                    <?php

                    if (isset($errores['imagen_error'])) {

                    ?>
                    <span id="error_imagen" class="small text-danger d-flex align-items-center"><svg
                            xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="-2 -3 24 24"
                            class="ms-1 me-1">
                            <path fill="currentColor"
                                d="m12.8 1.613l6.701 11.161c.963 1.603.49 3.712-1.057 4.71a3.2 3.2 0 0 1-1.743.516H3.298C1.477 18 0 16.47 0 14.581c0-.639.173-1.264.498-1.807L7.2 1.613C8.162.01 10.196-.481 11.743.517c.428.276.79.651 1.057 1.096M10 14a1 1 0 1 0 0-2a1 1 0 0 0 0 2m0-9a1 1 0 0 0-1 1v4a1 1 0 0 0 2 0V6a1 1 0 0 0-1-1" />
                        </svg><?= $errores['imagen_error'] ?></span>
                    <?php
                    }
                    ?>
                    <h3 class="mt-3 mb-1"><span class="fs-6 opacity-50">@</span><?= $usuario->login ?></h3>

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
                                        <a class="nav-link" href="#" data-section="infoClases"><i
                                                class="fas fa-calendar-alt me-2"></i>Clases</a>
                                        <a class="nav-link" href="#" data-section="solicitudes"><i
                                                class="fas fa-envelope me-2"></i>Solicitudes</a>
                                        <a class="nav-link" href="#" data-section="asistencia"><i
                                                class="fas fa-check-circle me-2"></i>Asistencia</a>
                                        <a class="nav-link" href="#" data-section="objetivos"><i
                                                class="fas fa-bullseye me-2"></i>Objetivos</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-9">
                                <div class="p-4">
                                    <!-- Personal Information -->
                                    <div class="mb-4 content-section" id="infoPersonal">
                                        <h5 class="mb-4">Información personal</h5>
                                        <form id="editForm" action="<?= RUTA_URL ?>/perfil/actualizarPerfil"
                                            method="POST">
                                            <input type="hidden" name="id" value="<?= $usuario->idUsuario ?>">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">login de usuario</label>
                                                    <input type="text" class="form-control editable"
                                                        value="<?= $usuario->login ?>" name="login" />
                                                    <?php if (isset($errores['login_error'])) { ?>
                                                    <span class="small text-danger d-flex align-items-center">
                                                        <?= $errores['login_error'] ?>
                                                    </span>
                                                    <?php } ?>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" class="form-control editable"
                                                        value="<?= $usuario->emailUsuario ?>" name="email" />
                                                    <?php if (isset($errores['email_error'])) { ?>
                                                    <span class="small text-danger d-flex align-items-center">
                                                        <?= $errores['email_error'] ?>
                                                    </span>
                                                    <?php } ?>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Nombre</label>
                                                    <input type="text" class="form-control editable"
                                                        value="<?= $usuario->nombreUsuario ?>" name="nombreUsuario" />
                                                    <?php if (isset($errores['nombreUsuario_error'])) { ?>
                                                    <span class="small text-danger d-flex align-items-center">
                                                        <?= $errores['nombreUsuario_error'] ?>
                                                    </span>
                                                    <?php } ?>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Primer Apellido</label>
                                                    <input type="text" class="form-control editable"
                                                        value="<?= $usuario->apellido1Usuario ?>"
                                                        name="apellido1Usuario" />
                                                    <?php if (isset($errores['apellido1Usuario_error'])) { ?>
                                                    <span class="small text-danger d-flex align-items-center">
                                                        <?= $errores['apellido1Usuario_error'] ?>
                                                    </span>
                                                    <?php } ?>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Segundo Apellido</label>
                                                    <input type="text" class="form-control editable"
                                                        value="<?= $usuario->apellido2Usuario ?>"
                                                        name="apellido2Usuario" />
                                                    <?php if (isset($errores['apellido2Usuario_error'])) { ?>
                                                    <span class="small text-danger d-flex align-items-center">
                                                        <?= $errores['apellido2Usuario_error'] ?>
                                                    </span>
                                                    <?php } ?>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Teléfono</label>
                                                    <input type="text" class="form-control editable"
                                                        value="<?= $usuario->telefonoUsuario ?>"
                                                        name="telefonoUsuario" />
                                                    <?php if (isset($errores['telefono_error'])) { ?>
                                                    <span class="small text-danger d-flex align-items-center">
                                                        <?= $errores['telefono_error'] ?>
                                                    </span>
                                                    <?php } ?>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Nueva contraseña</label>
                                                    <input type="password" class="form-control editable"
                                                        name="password" />
                                                    <?php if (isset($errores['password_error'])) { ?>
                                                    <span class="small text-danger d-flex align-items-center">
                                                        <?= $errores['password_error'] ?>
                                                    </span>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <button type="submit" id="saveButton"
                                                    class="btn btn-success d-none">Actualizar perfil</button>
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

                                    <div class="mb-4 content-section d-none" id="solicitudes">
                                        <h5 class="mb-4">Tus solicitudes</h5>
                                        @if (isset($solicitudes) && count($solicitudes) > 0)
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
                                                        @foreach ($solicitudes as $i => $solicitud)
                                                            <tr>
                                                                <td>{{ $solicitud->fechaSolicitud ?? '-' }}</td>
                                                                <td>{{ $solicitud->nombreAcademia ?? '-' }}</td>
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
                                                            <th>Entrenador</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($asistencias as $asistencia)
                                                            <tr>
                                                                <td>{{ $asistencia->fecha ?? '-' }}</td>
                                                                <td>{{ $asistencia->nombreAcademia ?? '-' }}</td>
                                                                <td>{{ $asistencia->nombreClase ?? '-' }}</td>
                                                                <td>{{ $asistencia->nombreEntrenador ?? '-' }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center">No tienes asistencias registradas.</div>
                                        @endif
                                    </div>

                                    <div class="mb-4 content-section d-none" id="objetivos">
                                        <h5 class="mb-4">Tus objetivos</h5>
                                        <div class="text-center text-muted">
                                            Aquí podrás ver y gestionar tus objetivos personales próximamente.
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

<script>
    $(document).ready(function() {
        $('#solicitudesTable').DataTable();
        $('#asistenciaTable').DataTable();
    });
</script>

<script>
    const RUTA_URL = '<?= RUTA_URL ?>';

    let USUARIO_ID = '<?= $usuario->idUsuario ?>';

    document.addEventListener("DOMContentLoaded", function() {
        const calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
            locale: 'es',
            initialView: 'dayGridMonth',
            editable: true,
            events: `${RUTA_URL}/calendarioController/get_clases_cliente?idUsuario=${USUARIO_ID}`,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek'
            },
            buttonText: {
                today: 'Hoy',
            },
            views: {
                dayGridMonth: {
                    buttonText: 'Mes'
                },
                timeGridWeek: {
                    buttonText: 'Semana'
                }
            },
            editable: false,
            droppable: false,
            eventDidMount: function(info) {
                const eventDate = new Date(info.event.start);
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                const dot = info.el.querySelector('.fc-daygrid-event-dot');
                if (dot) {
                    const color = eventDate < today ? '#ff870c' : '#46bc62';
                    dot.setAttribute('style', `border-color: ${color} !important`);
                }

                const entrenador = info.event.extendedProps.nombreEntrenador || 'Sin asignar';
                const horario =
                    `${info.event.start.toLocaleString()} - ${info.event.end ? info.event.end.toLocaleString() : ''}`;
                const apuntados = info.event.extendedProps.apuntados && info.event.extendedProps
                    .apuntados.length ?
                    info.event.extendedProps.apuntados.join(', ') :
                    'Nadie apuntado aún';

                new bootstrap.Tooltip(info.el, {
                    title: `<b>${info.event.title}</b><br>
                            <b>Entrenador:</b> ${entrenador}<br>
                            <b>Horario:</b> ${horario}<br>
                            <b>Apuntados:</b> ${apuntados}`,
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body',
                    html: true
                });
            },
            eventClick: function(info) {
                const idClase = info.event.id;
                const eventDate = new Date(info.event.start);
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                if (eventDate < today) {
                    Swal.fire(
                        'No permitido',
                        'Solo puedes desapuntarte de clases de hoy o futuras.',
                        'info'
                    );
                    return;
                }

                Swal.fire({
                    title: '¿Quieres desapuntarte?',
                    text: "Perderás tu reserva en esta clase.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, desapuntarme',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `${RUTA_URL}/calendarioController/desapuntarse`,
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                idClase: idClase,
                                idUsuario: USUARIO_ID
                            },
                            success: function(response) {
                                Swal.fire(
                                    '¡Hecho!',
                                    'Te has desapuntado de la clase.',
                                    'success'
                                );
                                info.event.remove();
                            },
                            error: function() {
                                Swal.fire(
                                    'Error',
                                    'No se pudo desapuntar de la clase.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            }

        });
    });

    document.addEventListener("DOMContentLoaded", function() {
        const navLinks = document.querySelectorAll("#perfilNav .nav-link");
        const sections = document.querySelectorAll(".content-section");

        navLinks.forEach(link => {
            link.addEventListener("click", function(e) {
                e.preventDefault();

                navLinks.forEach(nav => nav.classList.remove("active"));
                sections.forEach(section => section.classList.add("d-none"));

                this.classList.add("active");
                const sectionId = this.getAttribute("data-section");
                document.getElementById(sectionId).classList.remove("d-none");

                if (sectionId === "infoClases" && calendar) {
                    setTimeout(() => {
                        calendar.render();
                    }, 10);
                }
            });
        });

        if (document.querySelector('.nav-link.active').getAttribute('data-section') === 'infoClases' &&
            calendar) {
            setTimeout(() => {
                calendar.render();
            }, 10);
        }
    });
</script>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        const inputs = document.querySelectorAll(".editable");
        const saveButton = document.getElementById("saveButton");
        let originalValues = {};

        inputs.forEach(input => {
            originalValues[input.name] = input.value;

            input.addEventListener("input", () => {
                let modified = false;

                inputs.forEach(inp => {
                    if (inp.value !== originalValues[inp.name]) {
                        modified = true;
                    }
                });

                if (modified) {
                    saveButton.classList.remove("d-none");
                } else {
                    saveButton.classList.add("d-none");
                }
            });
        });
    });
</script>




@include('includes.footer')
