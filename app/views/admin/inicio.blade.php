@include('includes.header')

<link href="https://cdn.jsdelivr.net/npm/@coreui/coreui@5.3.1/dist/css/coreui.min.css" rel="stylesheet"
    integrity="sha384-PDUiPu3vDllMfrUHnurV430Qg8chPZTNhY8RUpq89lq22R3PzypXQifBpcpE1eoB" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="<?= RUTA_URL ?>/public/css/admin.css">
@php

    $usuario = json_decode($_SESSION['userLogin']['usuario']);
    $loginUsuario = $usuario->login; // o el campo que uses como nombre
    $rolUsuario = $usuario->rol; // o el campo que uses como nombre
    $nombreUsuario = $usuario->nombreUsuario;

@endphp

{{-- <h1><strong>{{ $nombreUsuario }}</strong></h1>

<h1 class="text-center tituloCalendario">Panel de administrador</h1>

<h4>Estadísticas de tipos de Academias</h4> --}}
{{-- <canvas id="miGrafico"></canvas> --}}
</div>
<div class="wrapper">
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <span class="text-white me-auto" onclick="window.location.href='<?= RUTA_URL ?>'">
                <img src="<?= RUTA_URL ?>/public/img/favicon/android-chrome-512x512.png" alt="Logo"
                    class="img-fluid" width="40" height="40" id="logo" title="Home" />
            </span>
        </div>
        <ul class="nav flex-column mt-4">
            <li class="nav-item"><a class="nav-link active" id="inicio-tab">Inicio</a></li>
            <li class="nav-item"><a class="nav-link" id="graficos-tab">Gráficos</a></li>
            <li class="nav-item"><a class="nav-link" id="usuarios-tab">Usuarios</a></li>
            {{-- <li class="nav-item"><a href="#" class="nav-link" id="configuracion-tab">Configuración</a></li> --}}
        </ul>
    </nav>

    <!-- Main content -->
    <div class="main">
        <h1>Bienvenido de nuevo</h1>
        <div class="container-fluid">
            <!-- Inicio -->
            <div id="inicio" class="tab-content active">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card p-3">
                            <div class="card-header">Ventas Mensuales</div>
                            <div class="card-body">
                                <canvas id="salesChart" height="150"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card p-4 text-center">
                            <div class="card-header">Resumen de Usuarios</div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <div class="stat-number">320</div>
                                    <div class="stat-label">Total de Usuarios</div>
                                </div>
                                <div>
                                    <div class="stat-number text-success">276</div>
                                    <div class="stat-label">Usuarios Activos</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos -->
            <div id="graficos" class="tab-content">
                <h2>Gráficos</h2>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card p-3">
                            <div class="card-header">Tipos de academia</div>
                            <div class="card-body">
                                <canvas id="miGrafico"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card p-3">
                            <div class="card-header">Usuarios por Región</div>
                            <div class="card-body">
                                <canvas id="barChart" height="150"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usuarios -->
            <div id="usuarios" class="tab-content">
                <h2>Datos de Usuarios</h2>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card p-3">
                            <div class="card-header">Total de Usuarios</div>
                            <div class="card-body">
                                <div class="stat-number">320</div>
                                <div class="stat-label">Usuarios Registrados</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card p-3">
                            <div class="card-header">Usuarios Activos</div>
                            <div class="card-body">
                                <div class="stat-number">276</div>
                                <div class="stat-label">Usuarios Activos</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuración -->
            <div id="configuracion" class="tab-content">
                <h2>Configuración</h2>
                <div class="card p-3">
                    <div class="card-header">Opciones de Configuración</div>
                    <div class="card-body">
                        <p>Ejemplo de configuración de la aplicación.</p>
                        <ul>
                            <li>Configurar notificaciones</li>
                            <li>Establecer permisos de usuario</li>
                            <li>Integración de pagos</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container" id="main">



    <?php
    // Asegúrate de que la variable $estadisticaAcademia tiene los datos correctos
    $estadisticaAcademiaJS = json_encode($estadisticaAcademia);
    ?>

    <script src="https://cdn.jsdelivr.net/npm/@coreui/coreui@5.3.1/dist/js/coreui.bundle.min.js"
        integrity="sha384-8QmUFX1sl4cMveCP2+H1tyZlShMi1LeZCJJxTZeXDxOwQexlDdRLQ3O9L78gwBbe" crossorigin="anonymous">
    </script>


    <script>
        if (document.querySelector('#navegacion')) {
            document.querySelector('#navegacion').style.display = 'none';
        }
    </script>

    <script>
        const tabs = document.querySelectorAll('.nav-link');
        const contents = document.querySelectorAll('.tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', (event) => {
                tabs.forEach(link => link.classList.remove('active'));
                contents.forEach(content => content.classList.remove('active'));

                tab.classList.add('active');
                document.getElementById(tab.id.replace('-tab', '')).classList.add('active');
            });
        });

        // Gráfico de línea (Ventas Mensuales)
        const salesChart = document.getElementById('salesChart').getContext('2d');
        new Chart(salesChart, {
            type: 'line',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
                datasets: [{
                    label: 'Ventas',
                    data: [100, 150, 130, 170, 200, 250],
                    borderColor: 'rgba(0, 122, 255, 1)',
                    backgroundColor: 'rgba(0, 122, 255, 0.2)',
                    fill: true
                }]
            }
        });

        // Gráfico de barras (Usuarios por Región)
        const barChart = document.getElementById('barChart').getContext('2d');
        new Chart(barChart, {
            type: 'bar',
            data: {
                labels: ['Norte', 'Sur', 'Este', 'Oeste'],
                datasets: [{
                    label: 'Usuarios',
                    data: [120, 150, 180, 100],
                    backgroundColor: 'rgba(0, 122, 255, 0.7)',
                    borderColor: 'rgba(0, 122, 255, 1)',
                    borderWidth: 1
                }]
            }
        });
    </script>

    <script>
        const ctx = document.getElementById('miGrafico').getContext('2d');

        // Pasamos los datos de PHP a JavaScript sin comillas adicionales
        const estadisticasAcademia = <?= $estadisticaAcademiaJS ?>;

        // Extraemos los nombres de los tipos de academia y los números de alumnos
        const labels = estadisticasAcademia.map(item => item.nombreTipo);
        const data = estadisticasAcademia.map(item => item.numAlumnos);

        // Crear el gráfico con los datos obtenidos
        const miGrafico = new Chart(ctx, {
            type: 'bar', // tipos: bar, line, pie, doughnut, radar, polarArea...
            data: {
                labels: labels,
                datasets: [{
                    label: 'Número de alumnos',
                    data: data,
                    backgroundColor: [
                        '#f87171', // Puedes modificar los colores o hacerlos dinámicos
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



    {{-- Aqui va a ir un calendario con la clases que se ha apuntado el usuario --}}

    {{-- y le salen las que ha ido y las que va a ir --}}

    @include('includes.footer')
