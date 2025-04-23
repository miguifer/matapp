</div>
<script src="https://cdn.jsdelivr.net/npm/less"></script> {{-- Carga LESS desde CDN (tiene que estar solo y en el footer) --}}
<script src="https://cdn.jsdelivr.net/npm/darkmode-js@1.5.7/lib/darkmode-js.min.js"></script> {{-- Carga Darkmode desde CDN --}}
<script src="{{ RUTA_URL }}/public/js/chatbot.js"></script> {{-- Carga JS genérico para el chatbot --}}
<script src="{{ RUTA_URL }}/public/js/variables.js"></script> {{-- Carga JS de variables --}}
<script src="{{ RUTA_URL }}/public/libs/DataTables/datatables.min.js"></script> {{-- Carga JS de datatables desde cdm --}}


{{-- <script>
    const options = {
        bottom: '22px', // Ajusta la altura del botón
        right: 'unset', // Lo coloca a la derecha
        left: '22px', // Asegura que no esté a la izquierda
        time: '0.5s', // Transición suave
        mixColor: '#fff',
        backgroundColor: '#fff', // Empieza en modo claro
        buttonColorDark: '#100f2c',
        buttonColorLight: '#fff',
        saveInCookies: true, // Guarda la preferencia del usuario
        label: '🌓', // Ícono del botón
        autoMatchOsTheme: false // Siempre empieza en modo claro
    };

    const darkmode = new Darkmode(options);
    darkmode.showWidget(); // Muestra el botón una sola vez

    // Asegurar que empiece en modo claro
    if (darkmode.isActivated()) {
        darkmode.toggle();
    }
</script> --}}
</body>

</html>
