@include('includes.header')

<?php
// Decodificamos la cadena JSON almacenada en la sesión
$usuario = json_decode($_SESSION['userLogin']['usuario']);
$userRole = $usuario->rol;

?>


<h1><strong>{{ $academia->nombreAcademia }}</strong></h1>


<button id="myButton" class="btn btn-primary" onclick="toggleRole()">Entrenador</button>


<div class="container mt-5">
    <h1 class="text-center tituloCalendario">Calendario de Eventos</h1>
    <div id="calendar"></div>
</div>



<script>
    let currentRole = "Entrenador"; // Valor inicial

    function toggleRole() {
        // Alternar el valor de la variable y el texto del botón
        if (currentRole === "Entrenador") {
            currentRole = "Cliente";
            document.getElementById("myButton").innerText = "Cliente";
        } else {
            currentRole = "Entrenador";
            document.getElementById("myButton").innerText = "Entrenador";
        }

        // Opcional: Mostrar el valor actual de la variable en la consola
        console.log(currentRole); // Esto mostrará "Cliente" o "Ntrenedor" en la consola
    }
</script>

<script>
    //crear una variable que contenga la ruta de la url en js
    const RUTA_URL = '<?= RUTA_URL ?>';

    const ACADEMIA_ID = '<?= $academia->idAcademia ?>';

    const ACADEMIA_ID_GERENTE = '<?= $academia->idGerente ?>';

    let USUARIO_ID = '<?= $usuario->idUsuario ?>';
    // USUARIO_ID = '23';

    const USUARIO_ROL = '<?= $userRole ?>';

    document.addEventListener("DOMContentLoaded", function() {
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
            locale: 'es',
            initialView: 'dayGridMonth',
            editable: true,
            events: `${RUTA_URL}/calendarioController/get_clases?idAcademia=${ {{ $academia->idAcademia }} }`, //url para obtener eventos con idAcademia
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek'
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
                // Mostrar tooltip con la descripción del evento
                const tooltip = new bootstrap.Tooltip(info.el, {
                    title: info.event.extendedProps.description || 'Sin descripción',
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body'
                });
            },
            eventClick: function(info) {
                // Si el usuario es Gerente, ya tienes la lógica para editar
                if ((USUARIO_ROL === 'Gerente' && ACADEMIA_ID_GERENTE === USUARIO_ID) ||
                    currentRole === 'Entrenador') {
                    // Lógica para editar el evento
                    Swal.fire({
                            icon: 'warning',
                            title: 'Editar Clase',
                            html: `
                <input type="text" id="title" class="swal2-input" value="${info.event.title}" placeholder="Título del Evento" required>
                <input type="text" id="start" class="swal2-input" placeholder="Fecha de inicio" value="${info.event.startStr}" required>
                <input type="text" id="end" class="swal2-input" placeholder="Fecha de fin" value="${info.event.endStr}">
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

                                if (!title || !start) {
                                    Swal.showValidationMessage(
                                        'Por favor, completa todos los campos');
                                }
                                return {
                                    title,
                                    start,
                                    end,
                                    id: info.event.id
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
                } else if (USUARIO_ROL === 'Cliente' ||
                    currentRole === 'Cliente') {
                    // Lógica para el cliente, mostrando un mensaje de confirmación de reserva
                    Swal.fire({
                        icon: 'info',
                        title: 'Confirmar Reserva',
                        text: `¿Estás seguro de que deseas reservar la clase "${info.event.title}"?`,
                        showCancelButton: true,
                        cancelButtonText: 'Cancelar',
                        confirmButtonText: 'Reservar',
                        confirmButtonColor: '#28a745'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Enviar la reserva de clase al servidor
                            $.ajax({
                                url: `${RUTA_URL}/calendarioController/reservar_clase`, // Cambia la URL al controlador que maneja la reserva
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    idClase: info.event.id,
                                    idUsuario: USUARIO_ID // El id del cliente
                                },
                                success: function(response) {
                                    if (response && response.message) {
                                        Swal.fire({
                                            title: '¡Éxito!',
                                            text: response.message,
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
                }
            },
            dateClick: function(info) {
                if (USUARIO_ROL !== 'Gerente' || ACADEMIA_ID_GERENTE !== USUARIO_ID) return;


                // Crear nuevo evento con SweetAlert2
                Swal.fire({
                    icon: 'info',
                    title: 'Agregar Clase',
                    confirmButtonText: 'Agregar',
                    html: `
        <input type="text" id="title" class="swal2-input" placeholder="Título de la clase" required>
        <input type="text" id="start" placeholder="Fecha de inicio" class="swal2-input" value="${info.dateStr}" required>
        <input type="text" id="end"  placeholder="Fecha de fin" class="swal2-input">
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
                        const idAcademia = Swal.getPopup().querySelector('#idAcademia')
                            .value;

                        if (!title || !start) {
                            Swal.showValidationMessage(
                                'Titulo y fecha de inicio son requeridos');
                        }
                        return {
                            title,
                            start,
                            end: Swal.getPopup().querySelector('#end').value,
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
        });

        calendar.render();
    });
</script>

<?php
if ($userRole == 'Gerente' || $userRole == 'Administrador') {
    echo '<div class="alert alert-info text-center mt-4">Eres gerente, tienes acceso a funciones administrativas.</div>';
?>

<h4>Estadísticas de tipos de Academias</h4>
<canvas id="miGrafico"></canvas>


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

<?php
} else {
    echo '<div class="alert alert-info text-center mt-4">Eres cliente, no tienes acceso a funciones administrativas.</div>';
 } ?>


@include('includes.footer')
