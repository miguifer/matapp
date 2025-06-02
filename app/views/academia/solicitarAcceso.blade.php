@include('includes.header')
<link rel="stylesheet" href="{{ RUTA_URL }}/public/css/academia.css">

<?php
$usuario = json_decode($_SESSION['userLogin']['usuario']);
$userRole = $usuario->rol;
?>

<div class="container mt-5">
    <ul class="nav nav-tabs mb-3" id="academiaTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button"
                role="tab" aria-controls="info" aria-selected="false">
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
            <button class="nav-link active" id="solicitud-tab" data-bs-toggle="tab" data-bs-target="#solicitud"
                type="button" role="tab" aria-controls="solicitud" aria-selected="true">
                Solicitar Acceso
            </button>
        </li>
    </ul>

    <div class="tab-content" id="academiaTabsContent">
        <div class="tab-pane fade" id="info" role="tabpanel" aria-labelledby="info-tab">
            <div class="mt-4">
                <h2>Información de la Academia</h2>
                <img src="{{ RUTA_IMG_ACADEMIAS . $academia->path_imagen }}" alt="Imagen academia"
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
            </div>
        </div>

        <div class="tab-pane fade show active" id="solicitud" role="tabpanel" aria-labelledby="solicitud-tab">
            <div class="mt-4">
                <h2>Solicitud de acceso a la academia</h2>
                <form action="{{ RUTA_URL }}/academia/solicitarAcceso" method="POST">
                    @csrf
                    <input type="hidden" name="idAcademia" value="{{ $academia->idAcademia }}">
                    <input type="hidden" name="idUsuario" value="{{ $usuario->idUsuario }}">
                    <button type="submit" class="btn btn-primary" id="solicitar-acceso">Crear Solicitud</button>
                    <p class="mt-3 text-muted" style="font-size: 0.rem;">Si quiere formar parte de esta academia, haga
                        clic en "Crear Solicitud".</p>
                </form>
            </div>
        </div>
    </div>
</div>

@include('includes.footer')
