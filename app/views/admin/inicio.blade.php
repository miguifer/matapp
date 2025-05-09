@include('includes.header')

<link href="<?= RUTA_URL ?>/libs/coreui-5.3.1-dist/css/coreui.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ RUTA_URL }}/public/libs/DataTables/datatables.min.css" />
<script src="{{ RUTA_URL }}/public/libs/chart.umd.js"></script>
<link rel="stylesheet" type="text/css" href="<?= RUTA_URL ?>/public/css/admin.css">


@php

    $usuario = json_decode($_SESSION['userLogin']['usuario']);
    $loginUsuario = $usuario->login;
    $rolUsuario = $usuario->rol;
    $nombreUsuario = $usuario->nombreUsuario;

@endphp


</div>
<div class="wrapper">
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
        </ul>
    </nav>

    @php
        $idsActivos = array_map(function ($u) {
            return is_object($u) ? $u->idUsuario : $u;
        }, $_SESSION['activos']);
    @endphp

    <div class="main">
        <h1>Panel de administrador</h1>
        <div class="container-fluid">
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
                    <div class="col-md-6">
                        <div class="card p-3">
                            <div class="card-header">Academias por modalidad</div>
                            <div class="card-body">
                                <canvas id="graficoAcademias"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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

        </div>
    </div>
</div>

<div class="modal fade" id="modalHistorico" tabindex="-1" aria-labelledby="modalHistoricoLabel" aria-hidden="true">
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
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalParticipantes" tabindex="-1" aria-labelledby="modalParticipantesLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalParticipantesLabel">Participantes de la clase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered" id="tablaParticipantes">
                    <thead>
                        <tr>
                            <th>Login</th>
                            <th>Nombre</th>
                            <th>Asistencia</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="container" id="main">



    <?php
    $estadisticaAcademiaJS = json_encode($estadisticaAcademia);
    ?>


    <script>
        window.RUTA_URL = "<?= RUTA_URL ?>";
        window.estadisticaAcademia = <?= json_encode($estadisticaAcademia) ?>;
        window.estadisticaAcademiaModalidad = @json($estadisticaAcademiaModalidad);
    </script>
    <script src="<?= RUTA_URL ?>/public/js/admin.js"></script>
    <script src="<?= RUTA_URL ?>/libs/coreui-5.3.1-dist/js/coreui.bundle.min.js"> </script>
    <script src="{{ RUTA_URL }}/public/libs/DataTables/datatables.min.js"></script> 


    @include('includes.footer')
