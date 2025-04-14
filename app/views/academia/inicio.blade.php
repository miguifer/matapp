@include('includes.header')

<?php
// Decodificamos la cadena JSON almacenada en la sesión
$usuario = json_decode($_SESSION['userLogin']['usuario']);

// Comprobamos si se ha decodificado correctamente y accedemos al rol
$userRole = isset($usuario->rol->nombreRol) ? $usuario->rol->nombreRol : 'Cliente';

// $userRole = 'cliente';

echo $userRole; // Esto te mostrará el rol del usuario;
?>

<h1>Bienvenido a la Academia <strong>{{ $academia->nombreAcademia }}</strong></h1>
<table id="academiaTable" class="display compact">
    <thead>
        <tr>
            <th>Nombre Academia</th>
            <th>ID Academia</th>
            <th>Ubicación Academia</th>
            <th>Tipo Academia</th>
            <th>ID Gerente</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $academia->idAcademia }}</td>
            <td>{{ $academia->nombreAcademia }}</td>
            <td>{{ $academia->ubicacionAcademia }}</td>
            <td>{{ $academia->tipoAcademia }}</td>
            <td>{{ $academia->idGerente }}</td>
        </tr>
    </tbody>
</table>

<script>
    $(document).ready(function() {
        $('#academiaTable').DataTable();
    });
</script>

<div class="container mt-5">
    <h1 class="text-center tituloCalendario">Calendario de Eventos</h1>
    <div id="calendar"></div>
</div>

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
                if (USUARIO_ROL === 'Gerente' && ACADEMIA_ID_GERENTE === USUARIO_ID) {
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
                } else if (USUARIO_ROL === 'Cliente') {
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
if ($userRole == 'Gerente') {
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


<?php
} else {
    echo '<div class="alert alert-info text-center mt-4">Eres cliente, no tienes acceso a funciones administrativas.</div>';
 } ?>


@include('includes.footer')
