// Configuración de toastr si hay mensaje (esto requiere que window.toastrMsg esté definido desde PHP)
if (window.toastrMsg) {
    toastr.options = {
        "closeButton": true,
        "positionClass": "toast-top-right",
        "timeOut": "10000",
        "progressBar": true,
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
    toastr.info(window.toastrMsg);
}

// DataTables para tablas de solicitudes y asistencia
$(document).ready(function () {
    $('#solicitudesTable').DataTable();
    $('#asistenciaTable').DataTable();
});

// Variables globales necesarias (deben definirse en el Blade antes de incluir este JS)
const RUTA_URL = window.RUTA_URL;
let USUARIO_ID = window.USUARIO_ID;
let calendar;

// Inicialización de FullCalendar
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

// Objetivos y barra de progreso
let clasesAsistidasSemana = 2;
let objetivoClases = localStorage.getItem('objetivoClases') || 3;
document.getElementById('objetivoClases').value = objetivoClases;

function actualizarBarra() {
    objetivoClases = parseInt(document.getElementById('objetivoClases').value) || 1;
    let porcentaje = Math.min(100, Math.round((clasesAsistidasSemana / objetivoClases) * 100));
    let barra = document.getElementById('progresoBarra');
    barra.style.width = porcentaje + '%';
    barra.textContent = `${clasesAsistidasSemana}/${objetivoClases}`;
    barra.className = 'progress-bar' + (porcentaje >= 100 ? ' bg-success' : ' bg-info');
}

document.getElementById('objetivoForm').addEventListener('submit', function (e) {
    e.preventDefault();
    objetivoClases = document.getElementById('objetivoClases').value;
    localStorage.setItem('objetivoClases', objetivoClases);
    actualizarBarra();
});
actualizarBarra();

// Lógica para reclamar recompensa (simulada)
document.getElementById('btnReclamarRecompensa').addEventListener('click', function () {
    let objetivo = parseInt(document.getElementById('objetivoClases').value) || 1;
    if (clasesAsistidasSemana >= objetivo) {
        document.getElementById('mensajeRecompensa').style.display = 'block';
        document.getElementById('mensajeRecompensa').className = 'alert alert-success mb-2';
        document.getElementById('mensajeRecompensa').textContent = '¡Felicidades! Has reclamado tu recompensa semanal.';
        // Aquí puedes hacer una petición AJAX para sumar monedas/puntos reales
    } else {
        document.getElementById('mensajeRecompensa').style.display = 'block';
        document.getElementById('mensajeRecompensa').className = 'alert alert-warning mb-2';
        document.getElementById('mensajeRecompensa').textContent = 'Aún no has alcanzado tu objetivo semanal.';
    }
});