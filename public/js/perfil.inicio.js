import { RUTA_URL } from "./variables.js";

// Configuración de toastr si hay mensaje 
if (typeof toastrMsg !== "undefined" && toastrMsg) {
    toastr.options = {
        "closeButton": true,
        "positionClass": "toast-top-right",
        "timeOut": "10000",
        "progressBar": true,
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
    toastr.info(toastrMsg);
}

// DataTables para tablas de solicitudes y asistencia
$(document).ready(function () {
    $('#solicitudesTable').DataTable();
    $('#asistenciaTable').DataTable();
});

let calendar;

// Inicialización de FullCalendar con mis clases
document.addEventListener("DOMContentLoaded", function () {
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;
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
            dayGridMonth: { buttonText: 'Mes' },
            timeGridWeek: { buttonText: 'Semana' }
        },
        editable: false,
        droppable: false,
        validRange: function (nowDate) {
            const start = new Date(nowDate);
            start.setMonth(start.getMonth() - 3);
            const end = new Date(nowDate);
            end.setMonth(end.getMonth() + 3);
            return { start: start, end: end };
        },
        eventDidMount: function (info) {
            const eventDate = new Date(info.event.start);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            const dot = info.el.querySelector('.fc-daygrid-event-dot');
            if (dot) {
                const color = eventDate < today ? '#ff870c' : '#46bc62';
                dot.setAttribute('style', `border-color: ${color} !important`);
            }

            const entrenador = info.event.extendedProps.nombreEntrenador || 'Sin asignar';
            const horario = `${info.event.start.toLocaleString()} - ${info.event.end ? info.event.end.toLocaleString() : ''}`;
            const apuntados = info.event.extendedProps.apuntados && info.event.extendedProps.apuntados.length ?
                info.event.extendedProps.apuntados.join(', ') : 'Nadie apuntado aún';

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
        //Desapuntarse de una clase
        eventClick: function (info) {
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
                        success: function (response) {
                            Swal.fire(
                                '¡Hecho!',
                                'Te has desapuntado de la clase.',
                                'success'
                            );
                            info.event.remove();
                        },
                        error: function () {
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

// Navegación de pestañas del perfil
document.addEventListener("DOMContentLoaded", function () {
    const navLinks = document.querySelectorAll("#perfilNav .nav-link");
    const sections = document.querySelectorAll(".content-section");

    navLinks.forEach(link => {
        link.addEventListener("click", function (e) {
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

    /// Fuerza la renderización del calendario 
    if (document.querySelector('.nav-link.active').getAttribute('data-section') === 'infoClases' && calendar) {
        setTimeout(() => {
            calendar.render();
        }, 10);
    }
});

// Mostrar botón de guardar solo si hay cambios en el formulario
document.addEventListener("DOMContentLoaded", function () {
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


// Hacer la función valorar global para que sea accesible desde el HTML
window.valorar = function (idClase, yaValorada) {
    if (yaValorada) {
        Swal.fire('Ya valorada', 'Ya has valorado esta clase.', 'info');
        return;
    }
    Swal.fire({
        title: 'Valora la clase',
        html: `
            <div id="star-rating" style="font-size:2rem;">
                <i class="fa fa-star" data-value="1" data-selected="0" style="color: #ccc;"></i>
                <i class="fa fa-star" data-value="2" data-selected="0" style="color: #ccc;"></i>
                <i class="fa fa-star" data-value="3" data-selected="0" style="color: #ccc;"></i>
                <i class="fa fa-star" data-value="4" data-selected="0" style="color: #ccc;"></i>
                <i class="fa fa-star" data-value="5" data-selected="0" style="color: #ccc;"></i>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Enviar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const selected = document.querySelectorAll('#star-rating .fa-star[data-selected="1"]').length;
            if (!selected) {
                Swal.showValidationMessage('Selecciona una puntuación');
            }
            return selected;
        },
        didOpen: () => {
            const stars = document.querySelectorAll('#star-rating .fa-star');
            let current = 0;
            stars.forEach((star, idx) => {
                star.addEventListener('mouseenter', function () {
                    stars.forEach((s, i) => {
                        s.style.color = i <= idx ? '#ffc107' : '#ccc';
                    });
                });
                star.addEventListener('mouseleave', function () {
                    stars.forEach((s, i) => {
                        s.style.color = i < current ? '#ffc107' : '#ccc';
                    });
                });
                star.addEventListener('click', function () {
                    current = idx + 1;
                    stars.forEach((s, i) => {
                        s.setAttribute('data-selected', i < current ? '1' : '0');
                        s.style.color = i < current ? '#ffc107' : '#ccc';
                    });
                });
            });
            document.getElementById('star-rating').addEventListener('mouseleave', function () {
                stars.forEach((s, i) => {
                    s.style.color = i < current ? '#ffc107' : '#ccc';
                });
            });
        }
    }).then(result => {
        if (result.isConfirmed && result.value) {
            const puntuacion = result.value;
            $.ajax({
                url: `${RUTA_URL}/calendarioController/valorar_clase`,
                type: 'POST',
                dataType: 'json',
                data: {
                    idClase: idClase,
                    valoracion: puntuacion, // Cambiado de 'puntuacion' a 'valoracion'
                    idUsuario: USUARIO_ID
                },
                success: function () {
                    Swal.fire('¡Gracias!', 'Tu valoración ha sido registrada.', 'success');
                    setTimeout(() => location.reload(), 1000);
                },
                error: function () {
                    Swal.fire('Error', 'No se pudo registrar la valoración.', 'error');
                }
            });
        }
    });
}
