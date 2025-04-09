@include('includes.header')

{{-- <link rel="stylesheet/less" type="text/css" href="{{ RUTA_URL }}/public/less/styles.less" />Carga LESS desde archivo local --}}
{{-- <link rel="stylesheet" type="text/css" href="{{ RUTA_URL }}/public/css/styles.css" /> Carga css desde archivo local --}}

{{-- start contenido --}}

<link rel="stylesheet" type="text/css" href="{{ RUTA_URL }}/public/css/fullcalendar.css" />

<div class="container mt-5">
    <h1 class="text-center">Calendario de Eventos</h1>
    <div id="calendar"></div>
</div>

<!-- Modal para Crear/Editar Evento -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="eventForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel">Agregar Evento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Título del Evento</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="start" class="form-label">Fecha de Inicio</label>
                        <input type="date" class="form-control" id="start" name="start" required>
                    </div>
                    <div class="mb-3">
                        <label for="end" class="form-label">Fecha de Fin</label>
                        <input type="date" class="form-control" id="end" name="end">
                    </div>
                    <input type="hidden" id="eventId" name="eventId">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" id="deleteEventBtn" class="btn btn-danger" style="display: none;">Eliminar
                        Evento</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    //crear una variable que contenga la ruta de la url en js
    const RUTA_URL = '<?= RUTA_URL ?>';

    document.addEventListener("DOMContentLoaded", function() {
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
            locale: 'es',
            initialView: 'dayGridMonth',
            editable: true,
            events: `${RUTA_URL}/paginas/get_events`, //url para obtener eventos
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            views: {
                dayGridMonth: {
                    buttonText: 'Mes'
                },
                timeGridWeek: {
                    buttonText: 'Semana'
                },
                timeGridDay: {
                    buttonText: 'Día'
                }
            },
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
                // Editar evento al hacer clic
                $('#eventModal').modal('show');
                $('#title').val(info.event.title);
                $('#start').val(info.event.startStr);
                $('#end').val(info.event.endStr);
                $('#eventId').val(info.event.id);
                $('#eventModalLabel').text('Editar Evento');
                $('#deleteEventBtn').show(); // Mostrar botón de eliminar
            },
            dateClick: function(info) {
                // Crear nuevo evento al hacer clic en una fecha
                $('#eventModal').modal('show');
                $('#eventForm')[0].reset();
                $('#start').val(info.dateStr);
                $('#end').val('');
                $('#eventId').val('');
                $('#eventModalLabel').text('Agregar Evento');
                $('#deleteEventBtn').hide(); // Ocultar botón de eliminar
            }
        });

        calendar.render();

        // Guardar evento (Agregar o Editar)
        $('#eventForm').on('submit', function(e) {
            e.preventDefault();
            const eventData = {
                title: $('#title').val(),
                start: $('#start').val(),
                end: $('#end').val(),
                id: $('#eventId').val()
            };



            $.ajax({
                url: eventData.id ? `${RUTA_URL}/paginas/update_event` : `${RUTA_URL}/paginas/add_event`,
                type: 'POST',
                dataType: 'json',
                data: eventData,
                success: function(response) {
                    console.log("Respuesta del servidor:", response);
                    alert(JSON.stringify(response)); // Para ver todo el contenido
                    $('#eventModal').modal('hide');
                    calendar.refetchEvents(); // Recargar eventos
                    if (response && response.message) {
                        alert(response.message);
                    } else {
                        alert("Error: respuesta no válida del servidor");
                    }
                },
                error: function() {
                    alert("Error al guardar el evento.");
                }
            });
        });

        // Eliminar evento
        $('#deleteEventBtn').on('click', function() {
            const eventId = $('#eventId').val();

            if (eventId) {
                $.ajax({
                    url: `${RUTA_URL}/paginas/delete_event`,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id: eventId
                    },
                    success: function(response) {
                        $('#eventModal').modal('hide');
                        calendar.refetchEvents(); // Recargar eventos
                        alert(response.message); // Mostrar mensaje de éxito o error
                    },
                    error: function() {
                        alert("Error al eliminar el evento.");
                    }
                });
            }
        });
    });
</script>


{{-- stop contenido --}}


{{-- <script src="{{ RUTA_URL }}/public/js/script.js"></script> Carga JS desde archivo local --}}


@include('includes.footer')