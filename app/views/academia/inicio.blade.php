@include('includes.header')

<?php
// Decodificamos la cadena JSON almacenada en la sesión
$usuario = json_decode($_SESSION['userLogin']['usuario']);
$userRole = $usuario->rol;

?>

<?php if ($usuario->rol == 'Entrenador') { ?>
<script>
    currentRole = "Entrenador"; // Valor inicial
</script>
<?php } else if ($usuario->rol == 'Alumno') { ?>
<script>
    currentRole = "Alumno"; // Valor inicial
</script>
<?php } else if ($usuario->rol == 'Gerente') { ?>
<script>
    currentRole = "Gerente"; // Valor inicial
</script>
<?php } else if ($usuario->rol == 'Administrador') { ?>
<script>
    currentRole = "Administrador"; // Valor inicial
</script>
<?php }else{
    redireccionar('/academia/solicitarAcceso?academia=' . urlencode(json_encode($academia)));
} ?>

<!-- Agrega los tabs de Bootstrap antes del contenido principal -->
<ul class="nav nav-tabs mb-3" id="academiaTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="calendario-tab" data-bs-toggle="tab" data-bs-target="#calendario" type="button" role="tab" aria-controls="calendario" aria-selected="true">
            Calendario
        </button>
    </li>
    @if ($usuario->rol == 'Gerente' || $usuario->rol == 'Administrador')
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="admin-tab" data-bs-toggle="tab" data-bs-target="#admin" type="button" role="tab" aria-controls="admin" aria-selected="false">
            Funciones Administrativas
        </button>
    </li>
    @endif
</ul>

<div class="tab-content" id="academiaTabsContent">
    <!-- Calendario -->
    <div class="tab-pane fade show active" id="calendario" role="tabpanel" aria-labelledby="calendario-tab">
        <h1><strong>{{ $academia->nombreAcademia }}</strong></h1>
        <img src="{{ $academia->path_imagen }}" alt="Imagen academia"
            style="width:80px; height:80px; object-fit:cover; border-radius:50%; margin-bottom:10px;">
        @if ($usuario->rol == 'Entrenador')
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="switchEntrenador" onchange="toggleRoleSwitch()" checked>
                <label class="form-check-label" for="switchEntrenador">
                    <span id="labelEntrenador">Entrenador</span>
                </label>
            </div>
        @endif
        @if ($usuario->rol == 'Gerente')
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="switchGerente" onchange="toggleRoleSwitch2()" checked>
                <label class="form-check-label" for="switchGerente">
                    <span id="labelGerente">Gerente</span>
                </label>
            </div>
        @endif
        <div class="container mt-5">
            <div id="calendar"></div>
        </div>
    </div>

    <!-- Funciones Administrativas -->
    @if ($usuario->rol == 'Gerente' || $usuario->rol == 'Administrador')
    <div class="tab-pane fade" id="admin" role="tabpanel" aria-labelledby="admin-tab">
        <div class="alert alert-info text-center mt-4">Eres {{ $usuario->rol }}, tienes acceso a funciones administrativas.</div>
        {{-- Aquí va TODO el contenido administrativo: estadísticas, tablas, scripts, etc. --}}
        <h2>Solicitudes de Academias</h2>
        <!-- ...pega aquí el contenido administrativo existente... -->
        <!-- Desde <h4>Estadísticas de tipos de Academias</h4> hasta el final de la sección admin -->
        <!-- Puedes mover todo el bloque PHP y HTML de funciones administrativas aquí -->
        {{-- ...contenido administrativo existente... --}}
        <?php
        // Asegúrate de que la variable $estadisticaAcademia tiene los datos correctos
        $estadisticaAcademiaJS = json_encode($estadisticaAcademia);
        ?>
        <script>
            const ctx = document.getElementById('miGrafico').getContext('2d');

            // Pasamos los datos de PHP a JavaScript sin comillas adicionales
            const estadisticasAcademia = <?= $estadisticaAcademiaJS ?>;

            // Extraemos los nombres de los tipos de academia y los números de alumnos
            const labels = estadisticasAcademia.map(item => item.nombreTipo);
            const data = estadisticasAcademia.map(item => item.numAlumnos);

            // Crear el gráfico con los datos obtenidos
            const miGrafico = new Chart(ctx, {
                type: 'bar', // tipos: bar, line, pie, doughnut, radar, polarArea...
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Número de alumnos',
                        data: data,
                        backgroundColor: [
                            '#f87171', // Puedes modificar los colores o hacerlos dinámicos
                            '#60a5fa',
                            '#34d399',
                            '#fbbf24'
                        ],
                        borderColor: '#111827',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>

        <h2>Solicitudes de Academias</h2>

        <table id="solicitudesTable" class="display compact">
            <thead>
                <tr>
                    <th>idSolicitud</th>
                    <th>idUsuario</th>
                    <th>Nombre Usuario</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($solicitudes as $solicitud)
                    <tr>
                        <td>{{ $solicitud->idSolicitud }}</td>
                        <td>{{ $solicitud->idUsuario }}</td>
                        <td>{{ $solicitud->nombreUsuario }}</td>
                        <td>
                            <button class="btn btn-success aceptarSolicitud" data-id="{{ $solicitud->idSolicitud }}"
                                data-id-Usuario="{{ $solicitud->idUsuario }}"
                                data-id-Academia="{{ $solicitud->idAcademia }}">Aceptar</button>
                            <button class="btn btn-danger rechazarSolicitud" data-id="{{ $solicitud->idSolicitud }}"
                                data-id-Usuario="{{ $solicitud->idUsuario }}"
                                data-id-Academia="{{ $solicitud->idAcademia }}">Rechazar</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <script>
            $(document).ready(function() {
                $('#solicitudesTable').DataTable();

                $('.aceptarSolicitud').on('click', function() {
                    const id = $(this).data('id');
                    const row = $(this).closest('tr');
                    const idUsuario = $(this).data('idUsuario');
                    const idAcademia = $(this).data('idAcademia');

                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¿Quieres aceptar esta solicitud?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Aceptar',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `${RUTA_URL}/solicitudesController/aceptar`,
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    id: id,
                                    idUsuario: idUsuario,
                                    idAcademia: idAcademia
                                },
                                success: function(response) {
                                    Swal.fire(
                                        '¡Aceptada!',
                                        'La solicitud ha sido aceptada.',
                                        'success'
                                );
                                    row.remove();
                                    setTimeout(function() {
                                        location.reload();
                                    }, 2000);
                                },
                                error: function() {
                                    Swal.fire(
                                        '¡Error!',
                                        'Hubo un problema al aceptar la solicitud.',
                                        'error'
                                );
                                
                                }
                            });
                        }
                    });
                });

                $('.rechazarSolicitud').on('click', function() {
                    const id = $(this).data('id');
                    const row = $(this).closest('tr');


                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¿Quieres rechazar esta solicitud?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Rechazar',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `${RUTA_URL}/solicitudesController/rechazar`,
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    id: id,
                                },
                                success: function(response) {
                                    Swal.fire(
                                        '¡Rechazada!',
                                        'La solicitud ha sido rechazada.',
                                        'success'
                                );
                                    row.remove();
                                    setTimeout(function() {
                                        location.reload();
                                    }, 2000);
                                },
                                error: function() {
                                    Swal.fire(
                                        '¡Error!',
                                        'Hubo un problema al rechazar la solicitud.',
                                        'error'
                                );

                                }
                            });
                        }
                    });
                });
            });
        </script>


        <h3>Alumnos</h3>


        <table id="alumnosTable" class="display compact">
            <thead>
                <tr>
                    <th>Nombre Usuario</th>
                    <th>Rol</th>
                    <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($alumnos as $alumno)
                    <tr>
                        <td>{{ $alumno->nombreUsuario }}</td>
                        <td>{{ $alumno->rol }}</td>
                        <td>
                            <button class="btn btn-danger eliminarAlumno"
                                data-id-Usuario="{{ $alumno->idUsuario }}">Eliminar</button>
                            @if ($alumno->rol !== 'Entrenador')
                                <button class="btn btn-primary hacerEntrenador"
                                    data-id-Usuario=" {{ $alumno->idUsuario }}  ">Hacer
                                    entrenador</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <script>
            $(document).ready(function() {
                $('#alumnosTable').DataTable();

                $('.eliminarAlumno').on('click', function() {
                    const row = $(this).closest('tr');
                    const idUsuario = $(this).data('idUsuario');
                    const idAcademia = {{ $academia->idAcademia }};

                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¿Quieres eliminar este alumno?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Aceptar',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `${RUTA_URL}/solicitudesController/eliminarAlumno`,
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    idUsuario: idUsuario,
                                    idAcademia: idAcademia
                                },
                                success: function(response) {
                                    Swal.fire(
                                        '¡Aceptada!',
                                        'El usuario ha sido eliminado.',
                                        'success'
                                );
                                    row.remove();
                                    setTimeout(function() {
                                        location.reload();
                                    }, 2000);
                                },
                                error: function() {
                                    Swal.fire(
                                        '¡Error!',
                                        'Hubo un problema al eliminar al usuario.',
                                        'error'
                                );
                                }
                            });
                        }
                    });
                });

                $('.hacerEntrenador').on('click', function() {
                    const row = $(this).closest('tr');
                    const idUsuario = $(this).data('idUsuario');
                    const idAcademia = {{ $academia->idAcademia }};

                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¿Quieres hacer entrenador a este alumno?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Aceptar',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `${RUTA_URL}/entrenadorController/hacerEntrenador`,
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    idUsuario: idUsuario,
                                    idAcademia: idAcademia
                                },
                                success: function(response) {
                                    Swal.fire(
                                        '¡Aceptada!',
                                        'El usuario es entrenador.',
                                        'success'
                                );
                                    row.remove();
                                    setTimeout(function() {
                                        location.reload();
                                    }, 2000);
                                },
                                error: function() {
                                    Swal.fire(
                                        '¡Error!',
                                        'Hubo un problema al hacer entrenador.',
                                        'error'
                                );
                                }
                            });
                        }
                    });
                });

            });
        </script>


        <h3>Entrenadores</h3>

        <table id="entrenadoresTable" class="display compact">
            <thead>
                <tr>
                    <th>Nombre entrenador</th>
                    <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($entrenadores as $entrenador)
                    <tr>
                        <td>{{ $entrenador->nombreUsuario }}</td>
                        <td>
                            <button class="btn btn-danger eliminarEntrenador"
                                data-id="{{ $entrenador->idUsuario }}">Eliminar</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <script>
            $(document).ready(function() {
                $('#entrenadoresTable').DataTable();

                $('.eliminarEntrenador').on('click', function() {
                    const id = $(this).data('id');
                    const row = $(this).closest('tr');
                    const idUsuario = {{ $entrenador->idUsuario }};
                    const idAcademia = {{ $academia->idAcademia }};

                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¿Quieres eliminar este entrenador?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Aceptar',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `${RUTA_URL}/entrenadorController/eliminarEntrenador`,
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    id: id,
                                    idUsuario: idUsuario,
                                    idAcademia: idAcademia
                                },
                                success: function(response) {
                                    Swal.fire(
                                        '¡Aceptada!',
                                        'El entrenador ha sido eliminado.',
                                        'success'
                                );
                                    row.remove();
                                    setTimeout(function() {
                                        location.reload();
                                    }, 2000);
                                },
                                error: function() {
                                    Swal.fire(
                                        '¡Error!',
                                        'Hubo un problema al eliminar al usuario.',
                                        'error'
                                );
                                }
                            });
                        }
                    });
                });

            });
        </script>
    </div>
    @endif
</div>


<script>
    function toggleRoleSwitch() {
        // Entrenador <-> Alumno
        if (currentRole === "Alumno") {
            currentRole = "Entrenador";
            document.getElementById("labelEntrenador").innerText = "Entrenador";
            document.getElementById("switchEntrenador").checked = true;
        } else {
            currentRole = "Alumno";
            document.getElementById("labelEntrenador").innerText = "Alumno";
            document.getElementById("switchEntrenador").checked = false;
        }
    }

    function toggleRoleSwitch2() {
        // Gerente <-> Alumno
        if (currentRole === "Alumno") {
            currentRole = "Gerente";
            document.getElementById("labelGerente").innerText = "Gerente";
            document.getElementById("switchGerente").checked = true;
        } else {
            currentRole = "Alumno";
            document.getElementById("labelGerente").innerText = "Alumno";
            document.getElementById("switchGerente").checked = false;
        }
    }
</script>


<script>
    //crear una variable que contenga la ruta de la url en js
    const RUTA_URL = '<?= RUTA_URL ?>';

    const ACADEMIA_ID = '<?= $academia->idAcademia ?>';

    const ACADEMIA_ID_GERENTE = '<?= $academia->idGerente ?>';

    let USUARIO_ID = '<?= $usuario->idUsuario ?>';
    // USUARIO_ID = '23';

    // const USUARIO_ROL = '<?= $userRole ?>';


    function obtenerUsuariosApuntados(idClase) {
        return $.ajax({
            url: `${RUTA_URL}/calendarioController/usuariosReservados`,
            type: 'POST',
            dataType: 'json',
            data: {
                idClase
            }
        });
    }


    // function obtenerUsuariosApuntados(idClase) {

    //     $.ajax({
    //         url: `${RUTA_URL}/calendarioController/usuariosReservados`,
    //         type: 'POST',
    //         dataType: 'json',
    //         data: {
    //             idClase
    //         }, // jQuery envía como application/x-www-form-urlencoded
    //         success: mostrarUsuarios,
    //         error(xhr, status, error) {
    //             console.error('Error al obtener usuarios:', error);
    //             Swal.fire('Error', 'No se pudieron cargar los usuarios.', 'error');
    //         }
    //     });

    // }

    // function mostrarUsuarios(usuarios) {
    //     console.log('Array de usuarios recibido:', usuarios);

    //     const contenedor = document.getElementById('lista-usuarios');
    //     contenedor.innerHTML = '';

    //     // Validamos que venga un Array
    //     if (!Array.isArray(usuarios)) {
    //         contenedor.textContent = 'Error: formato de datos inesperado.';
    //         return null;
    //     }

    //     // Si no hay usuarios
    //     if (usuarios.length === 0) {
    //         contenedor.textContent = 'No hay usuarios apuntados a esta clase.';
    //         return usuarios;
    //     }

    //     // Creamos un <ul> con cada usuario
    //     const ul = document.createElement('ul');
    //     usuarios.forEach(u => {
    //         const li = document.createElement('li');
    //         // Aquí asumes que el objeto usuario tiene .nombre y .email
    //         li.textContent = `${u.nombreUsuario ?? 'Nombre desconocido'} (${u.emailUsuario ?? 'Sin email'})`;
    //         ul.appendChild(li);
    //     });

    //     contenedor.appendChild(ul);
    //     return usuarios;
    // }




    document.addEventListener("DOMContentLoaded", function() {
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
            locale: 'es',
            initialView: 'dayGridMonth',
            editable: true,
            events: `${RUTA_URL}/calendarioController/get_clases?idAcademia=${ {{ $academia->idAcademia }} }`, //url para obtener eventos con idAcademia
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
            editable: false, // Permitir editar eventos
            droppable: false, // No permitir dropear eventos
            eventDidMount: function(info) {
                const eventDate = new Date(info.event.start);
                const today = new Date();
                today.setHours(0, 0, 0, 0); // Comparación sin hora

                const dot = info.el.querySelector('.fc-daygrid-event-dot');
                if (dot) {
                    const color = eventDate < today ? '#ff870c' : '#46bc62';
                    dot.setAttribute('style', `border-color: ${color} !important`);
                }

                // Tooltip
                new bootstrap.Tooltip(info.el, {
                    title: info.event.title || 'Sin Título',
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body'
                });
            },
            eventClick: function(info) {
                // Si el usuario es Gerente, ya tienes la lógica para editar
                if ((currentRole == 'Gerente' && ACADEMIA_ID_GERENTE === USUARIO_ID) ||
                    currentRole === 'Entrenador' || currentRole === 'Administrador') {



                    // Lógica para editar el evento
                    Swal.fire({
                            icon: 'warning',
                            title: 'Editar Clase',
                            html: `
                <input type="text" id="title" class="swal2-input" value="${info.event.title}" placeholder="Título del Evento" required>
                <input type="text" id="start" class="swal2-input" placeholder="Fecha de inicio" value="${info.event.startStr}" required>
                <input type="text" id="end" class="swal2-input" placeholder="Fecha de fin" value="${info.event.endStr}">
                <select id="idEntrenador" class="swal2-select">
                    <option value="" disabled selected>Selecciona un entrenador</option>
                    <option value="">Sin asignar</option>
                    @foreach ($entrenadores as $entrenador)
                    <option value="{{ $entrenador->idUsuario }}">{{ $entrenador->nombreUsuario }}</option>
                    @endforeach
                </select>
            `,
                            didOpen: () => {
                                flatpickr("#start", {
                                    enableTime: true,
                                    dateFormat: "Y-m-d H:i",
                                    time_24hr: true
                                });
                                flatpickr("#end", {
                                    enableTime: true,
                                    dateFormat: "Y-m-d H:i",
                                    time_24hr: true
                                });
                            },
                            preConfirm: () => {
                                const title = Swal.getPopup().querySelector('#title').value;
                                const start = Swal.getPopup().querySelector('#start').value;
                                const end = Swal.getPopup().querySelector('#end').value;
                                const idEntrenador = Swal.getPopup().querySelector(
                                    '#idEntrenador').value;

                                if (!title || !start) {
                                    Swal.showValidationMessage(
                                        'Por favor, completa todos los campos');
                                }
                                return {
                                    title,
                                    start,
                                    end,
                                    id: info.event.id,
                                    idEntrenador
                                };
                            },
                            showCancelButton: true,
                            cancelButtonText: 'Cancelar',
                            confirmButtonText: 'Editar Evento',
                            cancelButtonColor: '#6c757d',
                            showDenyButton: true,
                            denyButtonText: 'Eliminar Evento',
                            denyButtonColor: '#dc3545'
                        })
                        .then((result) => {
                            const eventData = result.value;
                            if (result.isConfirmed) {
                                // Guardar o editar evento
                                $.ajax({
                                    url: `${RUTA_URL}/calendarioController/update_clase`,
                                    type: 'POST',
                                    dataType: 'json',
                                    data: eventData,
                                    success: function(response) {
                                        if (response && response.message) {
                                            Swal.fire({
                                                title: 'Éxito!',
                                                text: response.message,
                                                icon: 'success',
                                                confirmButtonText: 'Genial'
                                            });
                                            calendar.refetchEvents();
                                        }
                                    },
                                    error: function() {
                                        Swal.fire({
                                            title: 'Error',
                                            text: 'Hubo un problema al guardar el evento.',
                                            icon: 'error',
                                            confirmButtonText: 'Cerrar'
                                        });
                                    }
                                });
                            } else if (result.isDenied) {
                                // Confirmación para eliminar
                                Swal.fire({
                                    title: '¿Estás seguro?',
                                    text: "Esta acción eliminará el evento permanentemente.",
                                    icon: 'question',
                                    showCancelButton: true,
                                    confirmButtonText: 'Sí, eliminar',
                                    confirmButtonColor: '#dc3545',
                                    cancelButtonText: 'Cancelar',
                                    reverseButtons: true
                                }).then((confirmResult) => {
                                    if (confirmResult.isConfirmed) {
                                        $.ajax({
                                            url: `${RUTA_URL}/calendarioController/delete_clase`,
                                            type: 'POST',
                                            dataType: 'json',
                                            data: {
                                                id: info.event.id
                                            },
                                            success: function(response) {
                                                if (response && response
                                                    .message) {
                                                    Swal.fire({
                                                        title: 'Éxito!',
                                                        text: response
                                                            .message,
                                                        icon: 'success',
                                                        confirmButtonText: 'Genial'
                                                    });
                                                    calendar
                                                        .refetchEvents();
                                                }
                                            },
                                            error: function() {
                                                Swal.fire({
                                                    title: 'Error',
                                                    text: 'Hubo un problema al eliminar el evento.',
                                                    icon: 'error',
                                                    confirmButtonText: 'Cerrar'
                                                });
                                            }
                                        });
                                    }
                                });
                            }
                        });
                } else if (currentRole === 'Alumno') {


                    // Lógica para el cliente, mostrando un mensaje de confirmación de reserva
                    $.ajax({
                        url: `${RUTA_URL}/calendarioController/usuariosReservados`,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            idClase: info.event.id
                        },
                        success: function(usuarios) {
                            // Crear el HTML con la lista de usuarios
                            let usuariosHtml = '';
                            if (usuarios.length === 0) {
                                usuariosHtml =
                                    '<p>No hay usuarios reservados para esta clase.</p>';
                            } else {
                                usuariosHtml = '<ul>';
                                usuarios.forEach(u => {
                                    usuariosHtml +=
                                        `<li>${u.nombreUsuario ?? 'Nombre desconocido'} </li>`;
                                });
                                usuariosHtml += '</ul>';
                            }

                            // Ahora mostramos el cuadro de confirmación con la lista de usuarios reservados
                            Swal.fire({
                                icon: 'info',
                                text: `¿Estás seguro de que deseas reservar la clase "${info.event.title}"?`,
                                html: `
                        <p><strong>${info.event.title}</strong></p>
                        <p>${info.event.start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })} - ${info.event.end ? info.event.end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : 'Sin fecha de fin'}</p>
                        <p>${info.event.extendedProps.nombreEntrenador || 'Entrenador no asignado'}</p>
                        <p><strong>Asistentes(${usuarios.length})</strong></p>
                        <div id="usuarios-reservados" style="margin-top: 10px;">
                            ${usuariosHtml} <!-- Aquí se agrega la lista de usuarios -->
                        </div>
                    `,
                                showCancelButton: true,
                                cancelButtonText: 'Cancelar',
                                confirmButtonText: 'Apúntate a la clase',
                                confirmButtonColor: '#28a745'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Enviar la reserva de clase al servidor
                                    $.ajax({
                                        url: `${RUTA_URL}/calendarioController/reservar_clase`,
                                        type: 'POST',
                                        dataType: 'json',
                                        data: {
                                            idClase: info.event.id,
                                            idUsuario: USUARIO_ID // El id del cliente
                                        },
                                        success: function(response) {
                                            if (response && response
                                                .message) {
                                                Swal.fire({
                                                    title: '¡Éxito!',
                                                    text: response
                                                        .message,
                                                    icon: 'success',
                                                    confirmButtonText: 'Genial'
                                                });
                                            }
                                        },
                                        error: function() {
                                            Swal.fire({
                                                title: 'Error',
                                                text: 'Hubo un problema al reservar la clase.',
                                                icon: 'error',
                                                confirmButtonText: 'Cerrar'
                                            });
                                        }
                                    });
                                }
                            });
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Error',
                                text: 'Hubo un problema al obtener los usuarios reservados.',
                                icon: 'error',
                                confirmButtonText: 'Cerrar'
                            });
                        }
                    });
                }
            },
            dateClick: function(info) {
                if ((currentRole == 'Gerente' && ACADEMIA_ID_GERENTE === USUARIO_ID) ||
                    currentRole === 'Entrenador' || currentRole === 'Administrador') {



                    // Crear nuevo evento con SweetAlert2
                    Swal.fire({
                        icon: 'info',
                        title: 'Agregar Clase',
                        confirmButtonText: 'Agregar',
                        html: `
        <input type="text" id="title" class="swal2-input" placeholder="Título de la clase" required>
        <input type="text" id="start" placeholder="Fecha de inicio" class="swal2-input" value="${info.dateStr}" required>
        <input type="text" id="end"  placeholder="Fecha de fin" class="swal2-input">
        <select id="idEntrenador" class="swal2-select">
            <option value="" disabled selected>Sin asignar entrenador</option>
            @foreach ($entrenadores as $entrenador)
            <option value="{{ $entrenador->idUsuario }}">{{ $entrenador->nombreUsuario }}</option>
            @endforeach
        </select>
        <input type="hidden" id="idAcademia" value="{{ $academia->idAcademia }}">

    `,
                        didOpen: () => {
                            flatpickr("#start", {
                                enableTime: true,
                                dateFormat: "Y-m-d H:i",
                                time_24hr: true
                            });
                            flatpickr("#end", {
                                enableTime: true,
                                dateFormat: "Y-m-d H:i",
                                time_24hr: true
                            });
                        },

                        preConfirm: () => {
                            const title = Swal.getPopup().querySelector('#title').value;
                            const start = Swal.getPopup().querySelector('#start').value;
                            const idAcademia = Swal.getPopup().querySelector(
                                    '#idAcademia')
                                .value;

                            if (!title || !start) {
                                Swal.showValidationMessage(
                                    'Titulo y fecha de inicio son requeridos');
                            }
                            return {
                                title,
                                start,
                                end: Swal.getPopup().querySelector('#end').value,
                                idEntrenador: Swal.getPopup().querySelector(
                                        '#idEntrenador')
                                    .value,
                                idAcademia
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const eventData = result.value;
                            $.ajax({
                                url: `${RUTA_URL}/calendarioController/add_clase`,
                                type: 'POST',
                                dataType: 'json',
                                data: eventData,
                                success: function(response) {
                                    if (response && response.message) {
                                        Swal.fire({
                                            title: 'Éxito!',
                                            text: response.message,
                                            icon: 'success',
                                            confirmButtonText: 'Genial'
                                        });
                                        calendar
                                            .refetchEvents(); // Recargar eventos
                                    }
                                },
                                error: function() {
                                    Swal.fire({
                                        title: 'Error',
                                        text: 'Hubo un problema al guardar el evento.',
                                        icon: 'error',
                                        confirmButtonText: 'Cerrar'
                                    });
                                }
                            });
                        }
                    });
                }
            }
        });

        calendar.render();

        // Actualizar varias veces con diferentes intervalos para garantizar renderizado correcto
        setTimeout(() => calendar.updateSize(), 100);
        setTimeout(() => calendar.updateSize(), 500);
        setTimeout(() => calendar.updateSize(), 1000);

        // También cuando la ventana cambie de tamaño
        window.addEventListener('resize', function() {
            calendar.updateSize();
        });

        // Y cuando se muestre la pestaña
        document.querySelector('#calendario-tab').addEventListener('shown.bs.tab', function() {
            calendar.updateSize();
        });
    });
</script>


@include('includes.footer')
