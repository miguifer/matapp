$(document).ready(function() {
    function cargarNotificaciones() {
        let $list = $('#notificaciones-list');
        $list.find('li:not(.dropdown-header)').remove();
        $('#notificaciones-loader').show();

        $.ajax({
            url: window.RUTA_URL + '/mensajesController/mensajesUsuario',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#notificaciones-loader').hide();
                let count = 0;
                if (data && data.success && Array.isArray(data.mensajes) && data.mensajes.length > 0) {
                    data.mensajes.forEach(function(n) {
                        count++;
                        $list.append(`
                            <li>
                                <a class="dropdown-item" href="${n.url || '#'}">
                                    <strong>${n.academia}</strong><br>
                                    "${n.mensaje}"<br>
                                    <small class="text-muted">${n.hace || ''}</small>
                                </a>
                            </li>
                        `);
                    });
                } else {
                    $list.append(
                        '<li class="text-center text-muted py-2">Sin notificaciones nuevas</li>'
                    );
                }
                $('#notificaciones-count').text(count);
            },
            error: function() {
                $('#notificaciones-loader').hide();
                $list.append(
                    '<li class="text-center text-danger py-2">Error al cargar notificaciones</li>'
                );
            }
        });
    }

    // Cargar notificaciones al cargar la página
    cargarNotificaciones();

    // También recargar cuando se hace click en el botón
    $('#dropdownNotificaciones').on('click', function() {
        cargarNotificaciones();
    });
});