import './variables.js';
import { RUTA_URL } from './variables.js';

//Inicializa las funciones administrativas de la academia
$(document).ready(function () {
    // Inicializa el DataTable para las solicitudes
    $('#solicitudesTable').DataTable();

    // Configuración de aceptar y rechazar solicitudes
    $('.aceptarSolicitud').on('click', function () {
        const id = $(this).data('id');
        const row = $(this).closest('tr');
        const idUsuario = $(this).data('idusuario');
        const idAcademia = $(this).data('idacademia');

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
                    success: function (response) {
                        Swal.fire(
                            '¡Aceptada!',
                            'La solicitud ha sido aceptada.',
                            'success'
                        );
                        row.remove();
                        setTimeout(function () {
                            location.reload();
                        }, 2000);
                    },
                    error: function () {
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

    $('.rechazarSolicitud').on('click', function () {
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
                    success: function (response) {
                        Swal.fire(
                            '¡Rechazada!',
                            'La solicitud ha sido rechazada.',
                            'success'
                        );
                        row.remove();
                        setTimeout(function () {
                            location.reload();
                        }, 2000);
                    },
                    error: function () {
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

    // Tabla de alumnos
    $('#alumnosTable').DataTable();

    // Funciones para eliminar y hacer entrenador a los alumnos
    $('.eliminarAlumno').on('click', function () {
        const row = $(this).closest('tr');
        const idUsuario = $(this).data('idusuario');
        const idAcademia = ACADEMIA_ID;

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
                    success: function (response) {
                        Swal.fire(
                            '¡Aceptada!',
                            'El usuario ha sido eliminado.',
                            'success'
                        );
                        row.remove();
                        setTimeout(function () {
                            location.reload();
                        }, 2000);
                    },
                    error: function () {
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

    $('.hacerEntrenador').on('click', function () {
        const row = $(this).closest('tr');
        const idUsuario = $(this).data('idusuario');
        const idAcademia = ACADEMIA_ID;

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
                    success: function (response) {
                        Swal.fire(
                            '¡Aceptada!',
                            'El usuario es entrenador.',
                            'success'
                        );
                        row.remove();
                        setTimeout(function () {
                            location.reload();
                        }, 2000);
                    },
                    error: function () {
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

    // Tabla de entrenadores
    $('#entrenadoresTable').DataTable({
        language: {
            emptyTable: "No hay entrenadores registrados."
        }
    });

    // Función para eliminar entrenadores
    $('.eliminarEntrenador').on('click', function () {
        const row = $(this).closest('tr');
        const idUsuario = $(this).data('id');
        const idAcademia = ACADEMIA_ID;

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
                        idUsuario: idUsuario,
                        idAcademia: idAcademia
                    },
                    success: function (response) {
                        Swal.fire(
                            '¡Eliminado!',
                            response && response.message ? response.message : 'El entrenador ha sido eliminado.',
                            'success'
                        );
                        row.remove();
                        setTimeout(function () {
                            location.reload();
                        }, 1500);
                    }
                });
            }
        });
    });

    // Botón editar academia
    $('#btnEditarAcademia').on('click', function () {
        Swal.fire({
            title: 'Editar información de la academia',
            html: `
                <input id="nombreAcademia" class="swal2-input" placeholder="Nombre" value="${ACADEMIA_NOMBRE}">
                <input id="ubicacionAcademia" class="swal2-input" placeholder="Ubicación" value="${ACADEMIA_UBICACION}">
            `,
            showCancelButton: true,
            confirmButtonText: 'Guardar',
            cancelButtonText: 'Cancelar',
            preConfirm: () => {
                return {
                    nombre: $('#nombreAcademia').val(),
                    ubicacion: $('#ubicacionAcademia').val(),
                    idAcademia: ACADEMIA_ID
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `${RUTA_URL}/academia/editarInfo`,
                    type: 'POST',
                    dataType: 'json',
                    data: result.value,
                    success: function (response) {
                        Swal.fire('¡Actualizado!', response.message || 'Información actualizada.', 'success')
                            .then(() => {
                                window.location.href = RUTA_URL;
                            });
                    },
                    error: function () {
                        Swal.fire('Error', 'No se pudo actualizar la información.', 'error');
                    }
                });
            }
        });
    });
});