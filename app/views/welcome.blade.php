    @include('includes.header')

    <link rel="stylesheet/less" type="text/css" href="{{ RUTA_URL }}/public/less/styles.less" />{{-- Carga LESS desde archivo local --}}
    <link rel="stylesheet" type="text/css" href="{{ RUTA_URL }}/public/css/styles.css" /> {{-- Carga css desde archivo local --}}

    {{-- start contenido --}}


    <h1 class="text-warning">Hola, {{ $name }}!</h1>
    <p>Bienvenido a mi proyecto usando BladeOneHTML.</p>
    <i class="fa-solid fa-house"></i>
    <ul>
        @foreach ($usuarios as $usuario)
            <li>{{ $usuario->email }}</li>
        @endforeach
    </ul>

    <p>{{ $ejemplo }}</p>

    <a href="{{ RUTA_URL }}/public/css/styles.css">Ruta normal a cualquier archivo público</a>
    <br>
    <a href="{{ RUTA_URL }}/paginas/calendario">Ir al método del controlador</a>



    <script>
        /* 
         *Da clase a los elementos de la lista con Jquery 
         */
        $(document).ready(function() {
            $("ul li").addClass("item");
        });
    </script>

    {{-- stop contenido --}}


    <script src="{{ RUTA_URL }}/public/js/script.js"></script> {{-- Carga JS desde archivo local --}}


    @include('includes.footer')
