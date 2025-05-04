@include('includes.header')

<link rel="stylesheet" href="{{ RUTA_URL }}/public/css/academia.css">

<?php
$usuario = json_decode($_SESSION['userLogin']['usuario']);
$userRole = $usuario->rol;

?>

<?php if ($usuario->rol == 'Entrenador') { ?>
<script>
    currentRole = "Entrenador"; 
</script>
<?php } else if ($usuario->rol == 'Alumno') { ?>
<script>
    currentRole = "Alumno"; 
</script>
<?php } else if ($usuario->rol == 'Gerente') { ?>
<script>
    currentRole = "Gerente"; 
</script>
<?php } else if ($usuario->rol == 'Administrador') { ?>
<script>
    currentRole = "Administrador"; 
</script>
<?php }else{
    redireccionar('/academia/solicitarAcceso?academia=' . urlencode(json_encode($academia)));
} ?>

<ul class="nav nav-tabs mb-3" id="academiaTabs" role="tablist">

    <li class="nav-item" role="presentation">
        <button class="nav-link" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab"
            aria-controls="info" aria-selected="false">
            Información
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="galeria-tab" data-bs-toggle="tab" data-bs-target="#galeria" type="button"
            role="tab" aria-controls="galeria" aria-selected="false">
            Galería
        </button>
    </li>

    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="calendario-tab" data-bs-toggle="tab" data-bs-target="#calendario"
            type="button" role="tab" aria-controls="calendario" aria-selected="true">
            Calendario
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="mensajes-tab" data-bs-toggle="tab" data-bs-target="#mensajes" type="button"
            role="tab" aria-controls="mensajes" aria-selected="false">
            Mensajes
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="ranking-tab" data-bs-toggle="tab" data-bs-target="#ranking" type="button"
            role="tab" aria-controls="ranking" aria-selected="false">
            Ranking Asistencia
        </button>
    </li>
    @if ($usuario->rol == 'Gerente' || $usuario->rol == 'Entrenador' || $usuario->rol == 'Administrador')
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="clases-tab" data-bs-toggle="tab" data-bs-target="#clases" type="button"
                role="tab" aria-controls="clases" aria-selected="false">
                Clases
            </button>
        </li>
    @endif
    @if ($usuario->rol == 'Gerente' || $usuario->rol == 'Administrador')
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="admin-tab" data-bs-toggle="tab" data-bs-target="#admin" type="button"
                role="tab" aria-controls="admin" aria-selected="false">
                Funciones Administrativas
            </button>
        </li>
    @endif

</ul>

<div class="tab-content" id="academiaTabsContent">
    <div class="tab-pane fade show active" id="calendario" role="tabpanel" aria-labelledby="calendario-tab">
        <h1><strong>{{ $academia->nombreAcademia }}</strong></h1>
        @if ($usuario->rol == 'Entrenador')
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="switchEntrenador" onchange="toggleRoleSwitch()"
                    checked>
                <label class="form-check-label" for="switchEntrenador">
                    <span id="labelEntrenador">Entrenador</span>
                </label>
            </div>
        @endif
        @if ($usuario->rol == 'Gerente')
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="switchGerente" onchange="toggleRoleSwitch2()"
                    checked>
                <label class="form-check-label" for="switchGerente">
                    <span id="labelGerente">Gerente</span>
                </label>
            </div>
        @endif
        <div class="container mt-5">
            <div id="calendar"></div>
        </div>
    </div>

    @if ($usuario->rol == 'Gerente' || $usuario->rol == 'Entrenador')
        <div class="tab-pane fade" id="clases" role="tabpanel" aria-labelledby="clases-tab">
            <h2>Clases de la Academia</h2>
            <table id="tablaClases" class="table table-striped">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th>Entrenador</th>
                        <th>Asistentes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($clases as $clase)
                        <tr>
                            <td>{{ $clase->title }}</td>
                            <td>{{ $clase->start }}</td>
                            <td>{{ $clase->end }}</td>
                            <td>{{ $clase->nombreEntrenador ?? 'Sin asignar' }}</td>
                            <td>
                                <button class="btn btn-primary btn-sm ver-asistentes" data-id="{{ $clase->id }}">
                                    Ver y confirmar
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if ($usuario->rol == 'Gerente' || $usuario->rol == 'Administrador')
        <div class="tab-pane fade" id="admin" role="tabpanel" aria-labelledby="admin-tab">
            
            <?php
            $estadisticaAcademiaJS = json_encode($estadisticaAcademia);
            ?>
            <script>
                const ctx = document.getElementById('miGrafico').getContext('2d');

                const estadisticasAcademia = <?= $estadisticaAcademiaJS ?>;

                const labels = estadisticasAcademia.map(item => item.nombreTipo);
                const data = estadisticasAcademia.map(item => item.numAlumnos);

                const miGrafico = new Chart(ctx, {
                    type: 'bar', 
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Número de alumnos',
                            data: data,
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

            <h2>Solicitudes de Academias</h2>

            <table id="solicitudesTable" class="display compact">
                <thead>
                    <tr>
                        <th>idSolicitud</th>
                        <th>idUsuario</th>
                        <th>Nombre Usuario</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($solicitudes as $solicitud)
                        <tr>
                            <td>{{ $solicitud->idSolicitud }}</td>
                            <td>{{ $solicitud->idUsuario }}</td>
                            <td>{{ $solicitud->nombreUsuario }}</td>
                            <td>
                                <button class="btn btn-success aceptarSolicitud"
                                    data-id="{{ $solicitud->idSolicitud }}"
                                    data-id-Usuario="{{ $solicitud->idUsuario }}"
                                    data-id-Academia="{{ $solicitud->idAcademia }}">Aceptar</button>
                                <button class="btn btn-danger rechazarSolicitud"
                                    data-id="{{ $solicitud->idSolicitud }}"
                                    data-id-Usuario="{{ $solicitud->idUsuario }}"
                                    data-id-Academia="{{ $solicitud->idAcademia }}">Rechazar</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>




            <h3>Alumnos</h3>

            <table id="alumnosTable" class="display compact">
                <thead>
                    <tr>
                        <th>Login</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Nombre</th>
                        <th>Primer Apellido</th>
                        <th>Segundo Apellido</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($alumnos as $alumno)
                        <tr>
                            <td>{{ $alumno->login }}</td>
                            <td>{{ $alumno->emailUsuario }}</td>
                            <td>{{ $alumno->telefonoUsuario }}</td>
                            <td>{{ $alumno->nombreUsuario }}</td>
                            <td>{{ $alumno->apellido1Usuario }}</td>
                            <td>{{ $alumno->apellido2Usuario }}</td>
                            <td>
                                <button class="btn btn-danger eliminarAlumno"
                                    data-id-Usuario="{{ $alumno->idUsuario }}">Eliminar</button>
                                @if ($alumno->rol !== 'Entrenador')
                                    <button class="btn btn-primary hacerEntrenador"
                                        data-id-Usuario="{{ $alumno->idUsuario }}">Hacer entrenador</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>



            <h3>Entrenadores</h3>

            <table id="entrenadoresTable" class="display compact">
                <thead>
                    <tr>
                        <th>Login</th>
                        <th>Nombre entrenador</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($entrenadores as $entrenador)
                        <tr>
                            <td>{{ $entrenador->login }}</td>
                            <td>{{ $entrenador->nombreUsuario }}</td>
                            <td>
                                <button class="btn btn-danger eliminarEntrenador"
                                    data-id="{{ $entrenador->idUsuario }}">Eliminar</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>


        </div>
    @endif

    <div class="tab-pane fade" id="info" role="tabpanel" aria-labelledby="info-tab">
        <div class="mt-4">
            <h2>Información de la Academia</h2>
            <img src="{{ $academia->path_imagen }}" alt="Imagen academia"
                style="width:120px; height:120px; object-fit:cover; border-radius:50%; margin-bottom:10px;">
            <p><strong>Nombre:</strong> {{ $academia->nombreAcademia }}</p>
            <p><strong>Tipo de academia:</strong> {{ $academia->tipoAcademia ?? 'Sin tipo disponible.' }}</p>
            <p><strong>Ubicación:</strong> {{ $academia->ubicacionAcademia ?? 'No especificada.' }}</p>
            @if (isset($academia->latitud, $academia->longitud))
                <div class="mt-3">
                    <iframe width="600px" height="350" frameborder="0" style="border:0"
                        src="https://www.google.com/maps?q={{ $academia->latitud }},{{ $academia->longitud }}&hl=es&z=16&t=k&output=embed"
                        allowfullscreen>
                    </iframe>
                </div>
            @endif
        </div>

        @if ($usuario->rol == 'Gerente' || $usuario->rol == 'Administrador')
            <button id="btnEditarAcademia" class="btn btn-secondary mb-3">Editar información</button>
        @endif


    </div>

    <div class="tab-pane fade" id="galeria" role="tabpanel" aria-labelledby="galeria-tab">
        <div class="mt-4">
            <h2>Galería de la Academia</h2>
            @php
                $galeriaDir =
                    $_SERVER['DOCUMENT_ROOT'] . "/matapp/public/data/academias-gallery/{$academia->idAcademia}";
                $galeriaUrl = "/matapp/public/data/academias-gallery/{$academia->idAcademia}";
                $imagenes = glob($galeriaDir . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
            @endphp

            <div class="galeria-flex">
                @if ($imagenes)
                    @foreach ($imagenes as $img)
                        <a href="#" data-img="{{ $galeriaUrl }}/{{ basename($img) }}">
                            <img src="{{ $galeriaUrl }}/{{ basename($img) }}" alt="Foto galería">
                        </a>
                    @endforeach
                @else
                    <p>No hay imágenes en la galería.</p>
                @endif
            </div>

            @if ($usuario->rol == 'Gerente' || $usuario->rol == 'Entrenador')
                <form action="{{ RUTA_URL }}/academia/subirFoto" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="idAcademia" value="{{ $academia->idAcademia }}">
                    <input type="file" name="foto" accept="image/*" required>
                    <button type="submit">Subir Foto</button>
                </form>
            @endif
        </div>
    </div>

    <div class="tab-pane fade" id="mensajes" role="tabpanel" aria-labelledby="mensajes-tab">
        <div class="mt-4">
            <h2>Mensajes</h2>

            @if ($usuario->rol == 'Entrenador' || $usuario->rol == 'Gerente' || $usuario->rol == 'Administrador')
                <form id="formEnviarMensaje" action="<?= RUTA_URL ?>/mensajesController/enviarMensaje" method="POST"
                    class="mb-3">
                    @csrf
                    <div class="mb-2">
                        <textarea name="mensaje" class="form-control" rows="3" placeholder="Escribe un mensaje..." required></textarea>
                    </div>
                    <input type="hidden" name="idAcademia" value="{{ $academia->idAcademia }}">
                    <input type="hidden" name="idUsuario" value="{{ $usuario->idUsuario }}">
                    <input type="hidden" name="fecha" value="{{ date('Y-m-d H:i:s') }}">
                    <button type="submit" class="btn btn-primary">Enviar mensaje</button>
                </form>
                <div id="mensajeEnviadoAlert"></div>
            @endif

            <div id="listaMensajes">
                @php
                    $mensajeFijado = null;
                    if (isset($mensajes) && count($mensajes) > 0) {
                        foreach ($mensajes as $msg) {
                            if (isset($msg->fijado) && $msg->fijado == 1) {
                                $mensajeFijado = $msg;
                                break;
                            }
                        }
                    }
                @endphp

                @if ($mensajeFijado)
                    <div class="alert alert-warning mb-3" style="border-left: 5px solid #ffc107;">
                        <strong style="color: #d48806;">
                            {{ $mensajeFijado->nombreUsuario ?? 'Usuario' }}
                            @if (isset($mensajeFijado->nombreRol))
                                ({{ $mensajeFijado->nombreRol }})
                            @endif
                            :
                        </strong>
                        <span>{{ $mensajeFijado->mensaje }}</span>
                        <br>
                        <small class="text-muted">{{ $mensajeFijado->fecha }} — <span
                                class="badge bg-warning text-dark">Fijado</span></small>
                        @if ($usuario->rol == 'Entrenador' || $usuario->rol == 'Gerente' || $usuario->rol == 'Administrador')
                            <button class="btn btn-sm btn-outline-secondary desfijar-mensaje"
                                data-id="{{ $mensajeFijado->idMensaje }}">Desfijar</button>
                        @endif
                    </div>
                @endif

                @if (isset($mensajes) && count($mensajes) > 0)
                    <ul class="list-group">
                        @foreach ($mensajes as $msg)
                            @if (!isset($msg->fijado) || $msg->fijado != 1)
                                <li class="list-group-item">
                                    <strong style="color: #007bff;">
                                        {{ $msg->nombreUsuario ?? 'Usuario' }}
                                        @if (isset($msg->nombreRol))
                                            ({{ $msg->nombreRol }})
                                        @endif
                                        :
                                    </strong>
                                    <span>{{ $msg->mensaje }}</span>
                                    <br>
                                    <small class="text-muted">{{ $msg->fecha }}</small>
                                    @if ($usuario->rol == 'Entrenador' || $usuario->rol == 'Gerente' || $usuario->rol == 'Administrador')
                                        <button class="btn btn-sm btn-outline-warning fijar-mensaje"
                                            data-id="{{ $msg->idMensaje }}"><i class="fas fa-thumbtack"></i>
                                        </button>
                                    @endif
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">No hay mensajes aún.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="ranking" role="tabpanel" aria-labelledby="ranking-tab">
        <h2>Ranking de Asistencia</h2>
        @if (count($ranking) > 0)
            <div class="d-flex justify-content-center align-items-end mb-4" style="gap: 40px;">
                <div class="text-center" style="order:1;">
                    @if (isset($ranking[1]))
                        <div style="font-size:2.2em;">&#x1F948;</div>
                        <div style="font-weight:bold;">{{ $ranking[1]->nombreUsuario }}</div>
                        <div
                            style="background:#c0c0c0;width:60px;height:40px;line-height:40px;margin:auto;border-radius:10px 10px 0 0;">
                            2º</div>
                    @endif
                </div>
                <div class="text-center" style="order:2;">
                    @if (isset($ranking[0]))
                        <div style="font-size:2.7em;">&#x1F451;</div>
                        <div style="font-weight:bold;">{{ $ranking[0]->nombreUsuario }}</div>
                        <div
                            style="background:gold;width:70px;height:60px;line-height:60px;margin:auto;border-radius:10px 10px 0 0;">
                            1º</div>
                    @endif
                </div>
                <div class="text-center" style="order:3;">
                    @if (isset($ranking[2]))
                        <div style="font-size:2.2em;">&#x1F949;</div>
                        <div style="font-weight:bold;">{{ $ranking[2]->nombreUsuario }}</div>
                        <div
                            style="background:#cd7f32;width:60px;height:30px;line-height:30px;margin:auto;border-radius:10px 10px 0 0;">
                            3º</div>
                    @endif
                </div>
            </div>
        @endif
        <table id="tablaRanking" class="table table-striped">
            <thead>
                <tr>
                    <th>Puesto</th>
                    <th>Alumno</th>
                    <th>Asistencias</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ranking as $i => $alumno)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            {{ $alumno->nombreUsuario }}
                            @if ($i == 0)
                                <span title="1er puesto" style="color: gold; font-size: 1.2em;">&#x1F451;</span>
                            @elseif ($i == 1)
                                <span title="2º puesto" style="color: silver; font-size: 1.2em;">&#x1F948;</span>
                            @elseif ($i == 2)
                                <span title="3er puesto" style="color: #cd7f32; font-size: 1.2em;">&#x1F949;</span>
                            @endif
                        </td>
                        <td>{{ $alumno->total_asistencias }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    const RUTA_URL = '<?= RUTA_URL ?>';
    const ACADEMIA_ID = '<?= $academia->idAcademia ?>';
    const ACADEMIA_ID_GERENTE = '<?= $academia->idGerente ?>';
    let USUARIO_ID = '<?= $usuario->idUsuario ?>';
    let currentRole = "<?= $usuario->rol ?>";
    const ACADEMIA_NOMBRE = '<?= addslashes($academia->nombreAcademia) ?>';
    const ACADEMIA_UBICACION = '<?= addslashes($academia->ubicacionAcademia ?? '') ?>';
</script>



<script src="{{ RUTA_URL }}/public/js/academia.js"></script>
<script src="{{ RUTA_URL }}/public/js/academia.calendario.js"></script>
<script src="{{ RUTA_URL }}/public/js/academia.admin.js"></script>
<script src="{{ RUTA_URL }}/public/js/academia.mensajes.js"></script>

@include('includes.footer')
