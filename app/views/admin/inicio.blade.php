@include('includes.header')

<link href="<?= RUTA_URL ?>/libs/coreui-5.3.1-dist/css/coreui.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="<?= RUTA_URL ?>/public/css/admin.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
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
            <li class="nav-item"><a class="nav-link" id="academias-tab">Academias</a></li>
            {{-- <li class="nav-item"><a href="#" class="nav-link" id="configuracion-tab">Configuración</a></li> --}}
        </ul>
    </nav>

    @php
        // Si $_SESSION['activos'] es un array de objetos con propiedad idUsuario
        $idsActivos = array_map(function ($u) {
            return is_object($u) ? $u->idUsuario : $u;
        }, $_SESSION['activos']);
    @endphp

    <!-- Main content -->
    <div class="main">
        <h1>Panel de administrador</h1>
        <div class="container-fluid">
            <!-- Inicio -->
            <div id="inicio" class="tab-content active">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card p-4 text-center" id="card-total-usuarios" style="cursor:pointer;">
                            <div class="card-header">Resumen de Usuarios</div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <div class="stat-number"><?= $estadisticaUsuarios ?></div>
                                    <div class="stat-label">Total de Usuarios</div>
                                </div>
                                <div>
                                    <div class="stat-number text-success"><?= count($_SESSION['activos']) ?></div>
                                    <div class="stat-label">Usuarios Activos</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card p-4 text-center" id="card-total-academias" style="cursor:pointer;">
                            <div class="card-header">Resumen de Academias</div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <div class="stat-number">{{ count($academias) }}</div>
                                    <div class="stat-label">Total de Academias</div>
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
                            <div class="card-header">Alumnos por modalidad</div>
                            <div class="card-body">
                                <canvas id="miGrafico"></canvas>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-md-6">
                        <div class="card p-3">
                            <div class="card-header">Usuarios por Región</div>
                            <div class="card-body">
                                <canvas id="barChart" height="150"></canvas>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>

            <!-- Usuarios -->
            <div id="usuarios" class="tab-content">
                <h2>Datos de Usuarios</h2>
                <div class="row">
                    <div class="col-12">
                        <div class="card p-3">
                            <div class="card-header">Listado de Usuarios</div>
                            <div class="card-body">
                                <table id="tablaUsuarios" class="table table-striped" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Login</th>
                                            <th>Email</th>
                                            <th>Teléfono</th>
                                            <th>Nombre</th>
                                            <th>Apellido 1</th>
                                            <th>Apellido 2</th>
                                            <th>Activo</th>
                                            <th>Online</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($usuarios as $u)
                                            <tr>
                                                <td>{{ $u->idUsuario }}</td>
                                                <td>{{ $u->login }}</td>
                                                <td>{{ $u->emailUsuario }}</td>
                                                <td>{{ $u->telefonoUsuario }}</td>
                                                <td>{{ $u->nombreUsuario }}</td>
                                                <td>{{ $u->apellido1Usuario }}</td>
                                                <td>{{ $u->apellido2Usuario }}</td>
                                                <td>
                                                    @if ($u->activo)
                                                        <span class="badge bg-success">Sí</span>
                                                    @else
                                                        <span class="badge bg-danger">No</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (in_array($u->idUsuario, $idsActivos))
                                                        <span class="badge bg-success">Online</span>
                                                    @else
                                                        <span class="badge bg-secondary">Offline</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academias -->
            <div id="academias" class="tab-content">
                <h2>Datos de Academias</h2>
                <div class="row">
                    <div class="col-12">
                        <div class="card p-3">
                            <div class="card-header">Listado de Academias</div>
                            <div class="card-body">
                                <table id="tablaAcademias" class="table table-striped" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Dirección</th>
                                            <th>Tipo</th>
                                            <th>ID Usuario gerente</th>
                                            <!-- Agrega más columnas si tu tabla tiene más campos -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($academias as $a)
                                            <tr class="fila-academia" data-id="{{ $a->idAcademia }}"
                                                style="cursor:pointer;">
                                                <td>{{ $a->idAcademia }}</td>
                                                <td>{{ $a->nombreAcademia }}</td>
                                                <td>{{ $a->ubicacionAcademia }}</td>
                                                <td>{{ $a->tipoAcademia }}</td>
                                                <td>{{ $a->idGerente }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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

    <script src="<?= RUTA_URL ?>/libs/coreui-5.3.1-dist/js/coreui.bundle.min.js"> </script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

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

    <script>
        $(document).ready(function() {
            $('#tablaUsuarios').DataTable();
            $('#tablaAcademias').DataTable();
        });

        // Al hacer clic en la tarjeta de usuarios, cambia a la pestaña de usuarios
        document.getElementById('card-total-usuarios').addEventListener('click', function() {
            document.getElementById('usuarios-tab').click();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Al hacer clic en la tarjeta de academias, cambia a la pestaña de academias
        document.getElementById('card-total-academias').addEventListener('click', function() {
            document.getElementById('academias-tab').click();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>



    <div class="modal fade" id="modalHistorico" tabindex="-1" aria-labelledby="modalHistoricoLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalHistoricoLabel">Histórico de Clases</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered" id="tablaHistorico">
                        <thead>
                            <tr>
                                <th>Inicio</th>
                                <th>Fin</th> 
                                <th>Nombre Clase</th>
                                <th>Instructor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Aquí se llenarán los datos por JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Evento al hacer clic en una fila de academia
            $('.fila-academia').on('click', function() {
                const idAcademia = $(this).data('id');
                $.ajax({
                    url: '<?= RUTA_URL ?>/admin/historicoClases/' + idAcademia,
                    method: 'GET',
                    success: function(res) {
                        let clases = [];
                        try {
                            clases = typeof res === 'string' ? JSON.parse(res) : res;
                        } catch (e) {
                            clases = [];
                        }
                        let html = '';
                        if (clases.length > 0) {
                            clases.forEach(function(clase) {
                                html += `<tr>
                            <td>${clase.start}</td>
                            <td>${clase.end ? clase.end : ''}</td> 
                            <td>${clase.title}</td>
                            <td>${clase.entrenador ?? ''}</td>
                        </tr>`;
                            });
                        } else {
                            html = '<tr><td colspan="4">No hay datos de clases.</td></tr>';
                        }
                        $('#tablaHistorico tbody').html(html);
                        $('#modalHistorico').modal('show');
                    },
                    error: function() {
                        $('#tablaHistorico tbody').html(
                            '<tr><td colspan="4">Error al cargar los datos.</td></tr>');
                        $('#modalHistorico').modal('show');
                    }
                });
            });

            // Cerrar el modal al pulsar la X
            $('#modalHistorico .btn-close').on('click', function() {
                $('#modalHistorico').modal('hide');
            });
        });
    </script>

    @include('includes.footer')
