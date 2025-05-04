function enviarMail(event) {
    event.preventDefault(); // Evita el comportamiento predeterminado de enviar el formulario

    const nombre = encodeURIComponent(
        document.getElementById("nombre").value
    );
    const tamaño = encodeURIComponent(
        document.getElementById("tamaño").value
    );
    const propietario = document.querySelector(
        'input[name="inlineRadioOptions"]:checked'
    ).value;
    const pais = encodeURIComponent(document.getElementById("pais").value);
    const prefijo = encodeURIComponent(
        document.getElementById("prefijo").value
    );
    const telefono = encodeURIComponent(
        document.getElementById("telefono").value
    );
    const instagram = encodeURIComponent(
        document.getElementById("instagram").value
    );

    const asunto = "Información de la Academia";
    const cuerpo =
        "Nombre de la academia: " + nombre + "%0A" +
        "Tamaño de la academia: " + tamaño + "%0A" +
        "¿Es propietario?: " + propietario + "%0A" +
        "País: " + pais + "%0A" +
        "Teléfono: " + prefijo + " " + telefono + "%0A" +
        "Instagram: @" + instagram;

    window.location.href = `mailto:mailermatapp@gmail.com?subject=${asunto}&body=${cuerpo}`;
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-register');
    if (form) {
        form.addEventListener('submit', enviarMail);
    }
});