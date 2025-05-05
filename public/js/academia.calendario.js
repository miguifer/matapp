document.addEventListener("DOMContentLoaded", function () {
    const calendarEl = document.getElementById('calendar');
    const today = new Date();
    const startDate = new Date(today);
    startDate.setMonth(today.getMonth() - 3);
    const endDate = new Date(today);
    endDate.setMonth(today.getMonth() + 3);

    const calendar = new FullCalendar.Calendar(calendarEl, {
        schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
        locale: 'es',
        initialView: 'dayGridMonth',
        editable: true,
        events: `${RUTA_URL}/calendarioController/get_clases?idAcademia=${ACADEMIA_ID}`,
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
        validRange: {
            start: startDate.toISOString().split('T')[0],
            end: endDate.toISOString().split('T')[0]
        },
        eventDidMount: function (info) {
            const eventDate = new Date(info.event.start);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            const dot = info.el.querySelector('.fc-daygrid-event-dot');
            if (dot) {
                const color = eventDate < today ? '#ff870c' : '#46bc62';
                dot.setAttribute('style', `border-color: ${color}!important`);
            }

            new bootstrap.Tooltip(info.el, {
                title: info.event.title || 'Sin Título',
                placement: 'top',
                trigger: 'hover',
                container: 'body'
            });
        },
        eventClick: function (info) {
            // Permisos de edición
            if ((currentRole == 'Gerente' && ACADEMIA_ID_GERENTE === USUARIO_ID) ||
                currentRole === 'Entrenador' || currentRole === 'Administrador') {

                const eventDate = new Date(info.event.start);
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                if (eventDate < today) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No permitido',
                        text: 'Solo puedes modificar clases de hoy o futuras.'
                    });
                    return;
                }

                Swal.fire({
                    icon: 'warning',
                    title: 'Editar Clase',
                    html: `
                        <input type="text" id="title" class="swal2-input" value="${info.event.title}" placeholder="Título del Evento" required>
                        <input type="text" id="start" class="swal2-input" placeholder="Fecha de inicio" value="${info.event.startStr}" required>
                        <input type="text" id="end" class="swal2-input" placeholder="Fecha de fin" value="${info.event.end ? info.event.endStr : ''}">
                        <select id="idEntrenador" class="swal2-select">
                            <option value="" disabled selected>Selecciona un entrenador</option>
                            <option value="">Sin asignar</option>
                        </select>
                    `,
                    didOpen: () => {
                        // Rellenar select de entrenadores si tienes la lista en JS
                        if (typeof ENTRENADORES !== 'undefined') {
                            const select = document.getElementById('idEntrenador');
                            ENTRENADORES.forEach(e => {
                                const opt = document.createElement('option');
                                opt.value = e.idUsuario;
                                opt.textContent = e.nombreUsuario;
                                if (info.event.extendedProps.idEntrenador == e.idUsuario) opt.selected = true;
                                select.appendChild(opt);
                            });
                        }
                        const today = new Date();
                        const maxDate = new Date();
                        maxDate.setMonth(today.getMonth() + 3);

                        const startPicker = flatpickr("#start", {
                            enableTime: true,
                            dateFormat: "Y-m-d H:i",
                            time_24hr: true,
                            minDate: today,
                            maxDate: maxDate,
                            onChange: function (selectedDates) {
                                if (selectedDates.length) {
                                    endPicker.set('minDate', selectedDates[0]);
                                }
                            }
                        });
                        const endPicker = flatpickr("#end", {
                            enableTime: true,
                            dateFormat: "Y-m-d H:i",
                            time_24hr: true,
                            minDate: today,
                            maxDate: maxDate
                        });
                        const startVal = document.getElementById('start').value;
                        if (startVal) endPicker.set('minDate', startVal);

                        const startInput = document.getElementById('start');
                        if (startInput.value) {
                            const date = startPicker.parseDate(startInput.value, "Y-m-d H:i") || new Date(startInput.value);
                            if (date) {
                                startInput.value = startPicker.formatDate(date, "Y-m-d H:i");
                            }
                        }
                    },
                    preConfirm: () => {
                        const title = Swal.getPopup().querySelector('#title').value;
                        const start = Swal.getPopup().querySelector('#start').value;
                        let end = Swal.getPopup().querySelector('#end').value;
                        const idEntrenador = Swal.getPopup().querySelector('#idEntrenador').value;
                        if (!title || !start) {
                            Swal.showValidationMessage('Por favor, completa todos los campos');
                        }
                        end = end.trim() === "" ? null : end;
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
                }).then((result) => {
                    const eventData = result.value;
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `${RUTA_URL}/calendarioController/update_clase`,
                            type: 'POST',
                            dataType: 'json',
                            data: eventData,
                            success: function (response) {
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
                            error: function () {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Hubo un problema al guardar el evento.',
                                    icon: 'error',
                                    confirmButtonText: 'Cerrar'
                                });
                            }
                        });
                    } else if (result.isDenied) {
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
                                    data: { id: info.event.id },
                                    success: function (response) {
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
                                    error: function () {
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
                const eventDate = new Date(info.event.start);
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                if (eventDate < today) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No permitido',
                        text: 'Solo puedes apuntarte a clases de hoy o futuras.'
                    });
                    return;
                }

                $.ajax({
                    url: `${RUTA_URL}/calendarioController/usuariosReservados`,
                    type: 'POST',
                    dataType: 'json',
                    data: { idClase: info.event.id },
                    success: function (usuarios) {
                        let usuariosHtml = '';
                        if (usuarios.length === 0) {
                            usuariosHtml = '<p>No hay usuarios reservados para esta clase.</p>';
                        } else {
                            usuariosHtml = '<ul>';
                            usuarios.forEach(u => {
                                usuariosHtml += `<li>
                                    ${u.imagen
                                        ? `<img src="${u.imagen}" alt="Imagen" style="width:24px;height:24px;border-radius:50%;margin-right:8px;vertical-align:middle;">`
                                        : `<span class="fa fa-user-circle" style="font-size:24px;color:#ccc;margin-right:2px;vertical-align:middle;"></span>`
                                    }
                                    ${u.nombreUsuario && u.nombreUsuario.trim() !== '' ? u.nombreUsuario : u.login}
                                </li>`;
                            });
                            usuariosHtml += '</ul>';
                        }
                        Swal.fire({
                            icon: 'info',
                            text: `¿Estás seguro de que deseas reservar la clase "${info.event.title}"?`,
                            html: `
                                <p><strong>${info.event.title}</strong></p>
                                <p>${info.event.start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })} - ${info.event.end
                                    ? (typeof info.event.end === 'string'
                                        ? new Date(info.event.end).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                                        : info.event.end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }))
                                    : 'Sin fecha de fin'
                                }</p>
                                <p><strong>Entrenador:</strong></p>
                                <p>
                                    ${info.event.extendedProps.imagenEntrenador
                                    ? `<img src="${info.event.extendedProps.imagenEntrenador}" alt="Entrenador" style="width:32px;height:32px;border-radius:50%;margin-right:8px;vertical-align:middle;">`
                                    : `<span class="fa fa-user-circle" style="font-size:32px;color:#ccc;margin-right:8px;vertical-align:middle;"></span>`
                                }
                                    ${info.event.extendedProps.nombreEntrenador && info.event.extendedProps.nombreEntrenador.trim() !== ''
                                    ? info.event.extendedProps.nombreEntrenador
                                    : (info.event.extendedProps.loginEntrenador || 'Entrenador no asignado')
                                }
                                </p>
                                <p><strong>Asistentes(${usuarios.length})</strong></p>
                                <div id="usuarios-reservados" style="margin-top: 10px;">
                                    ${usuariosHtml}
                                </div>
                            `,
                            showCancelButton: true,
                            cancelButtonText: 'Cancelar',
                            confirmButtonText: 'Apúntate a la clase',
                            confirmButtonColor: '#28a745'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: `${RUTA_URL}/calendarioController/reservar_clase`,
                                    type: 'POST',
                                    dataType: 'json',
                                    data: {
                                        idClase: info.event.id,
                                        idUsuario: USUARIO_ID
                                    },
                                    success: function (response) {
                                        if (response && response.message) {
                                            Swal.fire({
                                                title: '¡Éxito!',
                                                text: response.message,
                                                icon: 'success',
                                                confirmButtonText: 'Genial'
                                            });
                                        }
                                    },
                                    error: function () {
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
                    error: function () {
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
        dateClick: function (info) {
            const clickedDate = new Date(info.dateStr);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (clickedDate < today) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No permitido',
                    text: 'Solo puedes crear clases para hoy o fechas futuras.'
                });
                return;
            }

            Swal.fire({
                icon: 'info',
                title: 'Agregar Clase',
                confirmButtonText: 'Agregar',
                html: `
                    <input type="text" id="title" class="swal2-input" placeholder="Título de la clase" required>
                    <input type="text" id="start" placeholder="Fecha de inicio" class="swal2-input" value="${info.dateStr}" required>
                    <input type="text" id="end" placeholder="Fecha de fin" class="swal2-input">
                    <select id="idEntrenador" class="swal2-select">
                        <option value="" disabled selected>Sin asignar entrenador</option>
                    </select>
                    <input type="hidden" id="idAcademia" value="${ACADEMIA_ID}">
                `,
                didOpen: () => {
                    // Rellenar select de entrenadores si tienes la lista en JS
                    if (typeof ENTRENADORES !== 'undefined') {
                        const select = document.getElementById('idEntrenador');
                        ENTRENADORES.forEach(e => {
                            const opt = document.createElement('option');
                            opt.value = e.idUsuario;
                            opt.textContent = e.nombreUsuario;
                            select.appendChild(opt);
                        });
                    }
                    const today = new Date();
                    const maxDate = new Date();
                    maxDate.setMonth(today.getMonth() + 3);

                    const startPicker = flatpickr("#start", {
                        enableTime: true,
                        dateFormat: "Y-m-d H:i",
                        time_24hr: true,
                        minDate: today,
                        maxDate: maxDate,
                        onChange: function (selectedDates) {
                            if (selectedDates.length) {
                                endPicker.set('minDate', selectedDates[0]);
                            }
                        }
                    });
                    const endPicker = flatpickr("#end", {
                        enableTime: true,
                        dateFormat: "Y-m-d H:i",
                        time_24hr: true,
                        minDate: today,
                        maxDate: maxDate
                    });
                    const startVal = document.getElementById('start').value;
                    if (startVal) endPicker.set('minDate', startVal);
                },
                preConfirm: () => {
                    const title = Swal.getPopup().querySelector('#title').value;
                    const start = Swal.getPopup().querySelector('#start').value;
                    const idAcademia = Swal.getPopup().querySelector('#idAcademia').value;
                    if (!title || !start) {
                        Swal.showValidationMessage('Titulo y fecha de inicio son requeridos');
                    }
                    return {
                        title,
                        start,
                        end: Swal.getPopup().querySelector('#end').value,
                        idEntrenador: Swal.getPopup().querySelector('#idEntrenador').value,
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
                        success: function (response) {
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
                        error: function () {
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

    setTimeout(() => calendar.updateSize(), 100);
    setTimeout(() => calendar.updateSize(), 500);
    setTimeout(() => calendar.updateSize(), 1000);

    window.addEventListener('resize', function () {
        calendar.updateSize();
    });

    const tab = document.querySelector('#calendario-tab');
    if (tab) {
        tab.addEventListener('shown.bs.tab', function () {
            calendar.updateSize();
        });
    }
});

$(document).ready(function () {
    $('#tablaClases').DataTable();

    $('.ver-asistentes').on('click', function () {
        const idClase = $(this).data('id');
        $.ajax({
            url: `${RUTA_URL}/calendarioController/usuariosReservados`,
            type: 'POST',
            dataType: 'json',
            data: { idClase },
            success: function (usuarios) {
                let html = '<form id="formAsistencia">';
                usuarios.forEach(u => {
                    html += `
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="asistencia[]" value="${u.idUsuario}" id="asist_${u.idUsuario}" ${u.asistencia == 1 ? 'checked' : ''}>
                        ${u.imagen
                            ? `<img src="${u.imagen}" alt="Imagen" style="width:32px;height:32px;border-radius:50%;margin-right:8px;vertical-align:middle;">`
                            : `<span class="fa fa-user-circle" style="font-size:32px;color:#ccc;margin-right:8px;vertical-align:middle;"></span>`
                        }
                            <label class="form-check-label" for="asist_${u.idUsuario}">
                                ${(u.nombreUsuario && u.nombreUsuario.trim() !== '') ? u.nombreUsuario : u.login}
                            </label>
                        </div>
                    `;
                });
                html += `<input type="hidden" name="idClase" value="${idClase}"></form>`;

                Swal.fire({
                    title: 'Confirmar asistencia',
                    html: html,
                    showCancelButton: true,
                    confirmButtonText: 'Guardar asistencia',
                    preConfirm: () => {
                        const form = document.getElementById('formAsistencia');
                        let formData = $(form).serialize();
                        if (!formData.includes('asistencia%5B%5D')) {
                            formData += '&asistencia[]=';
                        }
                        return new Promise((resolve, reject) => {
                            $.ajax({
                                url: `${RUTA_URL}/calendarioController/confirmarAsistencia`,
                                type: 'POST',
                                dataType: 'json',
                                data: formData,
                                success: function (response) {
                                    resolve(response);
                                },
                                error: function (xhr) {
                                    reject(xhr.responseJSON && xhr.responseJSON.message
                                        ? xhr.responseJSON.message
                                        : 'Hubo un problema al guardar la asistencia.'
                                    );
                                }
                            });
                        });
                    }
                });
            }
        });
    });
});