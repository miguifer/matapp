    @include('includes.header')

    <link rel="stylesheet/less" type="text/css" href="{{ RUTA_URL }}/public/less/styles.less" />{{-- Carga LESS desde archivo local --}}
    <link rel="stylesheet" type="text/css" href="{{ RUTA_URL }}/public/css/styles.css" /> {{-- Carga css desde archivo local --}}
    <link rel="stylesheet" type="text/css" href="{{ RUTA_URL }}/public/css/fontawesome.css" /> {{-- Carga css desde archivo local --}}
    <link rel="stylesheet" type="text/css" href="{{ RUTA_URL }}/public/css/datatables.css" /> {{-- Carga css datatables desde archivo local --}}
    {{-- Carga css desde archivo local --}}
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


    <table id="myTable" class="display">
        <thead>
            <tr>
                <th>Id Usuario</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->id_usuario }}</td>
                    <td>{{ $usuario->nombre }}</td>
                    <td>{{ $usuario->email }}</td>
                    <td>{{ $usuario->telefono }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <canvas id="miGrafico"></canvas>
    
    <script>
        const ctx = document.getElementById('miGrafico').getContext('2d');
        const miGrafico = new Chart(ctx, {
            type: 'bar', // tipos: bar, line, pie, doughnut, radar, polarArea...
            data: {
                labels: ['Boxeo', 'Jiu-Jitsu', 'Muay Thai', 'MMA'],
                datasets: [{
                    label: 'Número de alumnos',
                    data: [12, 19, 7, 14],
                    backgroundColor: [
                        '#f87171',
                        '#60a5fa',
                        '#34d399',
                        '#fbbf24'
                    ],
                    borderColor: '#111827',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>


    <script>
        /* 
         *Da clase a los elementos de la lista con Jquery 
         */
        $(document).ready(function() {
            $("ul li").addClass("item");
        });


        /* 
         *Ejemplo de uso de DataTable con Jquery 
         */

        $(document).ready(function() {
            $('#myTable').DataTable();
        });
    </script>

    {{-- stop contenido --}}


    <script src="{{ RUTA_URL }}/public/js/script.js"></script> {{-- Carga JS desde archivo local --}}


    @include('includes.footer')
