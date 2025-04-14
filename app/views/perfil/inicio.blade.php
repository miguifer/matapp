@include('includes.header')


@php

    $usuario = json_decode($_SESSION['userLogin']['usuario']);

@endphp

<h1>PERFIL <strong>{{ $usuario->login }}</strong></h1>

<div class="container mt-5">
    <h1 class="text-center tituloCalendario">Calendario de Mis Reservas</h1>
    <div id="calendar"></div>
</div>

<script>
    //crear una variable que contenga la ruta de la url en js
    const RUTA_URL = '<?= RUTA_URL ?>';

    let USUARIO_ID = '<?= $usuario->idUsuario ?>';


    document.addEventListener("DOMContentLoaded", function() {
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
            locale: 'es',
            initialView: 'dayGridMonth',
            editable: true,
            events: `${RUTA_URL}/calendarioController/get_clases_cliente?idUsuario=${USUARIO_ID}`,
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
            editable: false,
            droppable: false,
            eventDidMount: function(info) {
                const tooltip = new bootstrap.Tooltip(info.el, {
                    title: info.event.extendedProps.description || 'Sin descripción',
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body'
                });
            }
        });
        calendar.render();
    });
</script>



{{-- Aqui va a ir un calendario con la clases que se ha apuntado el usuario --}}

{{-- y le salen las que ha ido y las que va a ir --}}

@include('includes.footer')
