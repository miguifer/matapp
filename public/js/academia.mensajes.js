$(document).ready(function () {
    $('#formEnviarMensaje').on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        var btn = form.find('button[type="submit"]');
        btn.prop('disabled', true);

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function (response) {
                // Si el backend responde con {success: true, message: "..."}

                if (response && response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Mensaje enviado',
                        text: response.message ||
                            'Tu mensaje ha sido enviado correctamente.'
                    }).then(() => {
                        location
                            .reload(); // Recarga la página después de cerrar el SweetAlert
                    });
                    form[0].reset();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response && response.message ? response.message :
                            'No se pudo enviar el mensaje.'
                    });
                }
            },
            error: function (xhr) {
                let msg = 'No se pudo enviar el mensaje.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: msg
                });
            },
            complete: function () {
                btn.prop('disabled', false);
            }
        });
    });
});