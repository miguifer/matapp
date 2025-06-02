@include('includes.header')

<link rel="stylesheet" href="{{ RUTA_URL }}/public/css/home.css" type="text/css" />
<link rel="stylesheet/less" type="text/css" href="{{ RUTA_URL }}/public/css/styles/matapp-inicio.less" />

<div class="search-container" style="margin-top: 25vh">
    <h4 class="">Encuentra tu academia</h4>
    <div class="search-bar d-flex">
        <input class="w-100 p-2" type="text" title="Busca por nombre de academia" id="searchInput"
            placeholder="Buscar..." autocomplete="off" />
        <img src="{{ RUTA_URL }}/public/img/home/lupita.svg" id="lupita" alt="lupa de buscar" />
    </div>
    <span class="input-subtitle">Empieza a escribir para encontrar tu academia</span><br />
    <div id="resultados"></div>
    <a href="{{ RUTA_URL }}/crearAcademia" class="small text-decoration-none" id="link-crear-academia">¿No puedes
        encontrar tu
        academia? ¡Crea una!</a>
</div>

<div class="rankings">

    @if (isset($mejoresEntrenadores) && count($mejoresEntrenadores) > 0)
        <div class="ranking-entrenadores mt-5">
            <h5 class="mb-3"><i class="fa-solid fa-ranking-star"></i> Entrenadores MatApp</h5>
            <ol class="list-group list-group-numbered">
                @foreach ($mejoresEntrenadores as $i => $entrenador)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="d-flex align-items-center" style="gap: 0.5em;">
                            @php
                                $medalla = '';
                                if ($i === 0) {
                                    $medalla = '🥇';
                                } elseif ($i === 1) {
                                    $medalla = '🥈';
                                } elseif ($i === 2) {
                                    $medalla = '🥉';
                                }
                            @endphp
                            @if ($medalla)
                                <span style="font-size: 1.3em;">{{ $medalla }}</span>
                            @endif
                            {{ $entrenador->nombre ?? 'Sin nombre' }}
                            @if (!empty($entrenador->especialidad))
                                <small class="text-muted">({{ $entrenador->especialidad }})</small>
                            @endif
                            @if (!empty($entrenador->nombreAcademia))
                                <small class="text-muted ms-2">- {{ $entrenador->nombreAcademia }}</small>
                            @endif
                        </span>
                        <span class="badge bg-primary rounded-pill d-flex align-items-center" style="gap: 0.3em;">
                            {{ $entrenador->puntuacion ?? '0' }}
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="gold"
                                class="bi bi-star-fill ms-1" viewBox="0 0 16 16">
                                <path
                                    d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.32-.158-.888.283-.95l4.898-.696 2.197-4.386c.197-.392.73-.392.927 0l2.197 4.386 4.898.696c.441.062.612.63.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                            </svg>
                        </span>
                    </li>
                @endforeach
            </ol>
        </div>
    @endif

    @if (isset($mejoresAcademias) && count($mejoresAcademias) > 0)
        <div class="ranking-academias mt-5">
            <h5 class="mb-3"><i class="fa-solid fa-trophy"></i> Academias con más alumnos</h5>
            <ol class="list-group list-group-numbered">
                @foreach ($mejoresAcademias as $i => $academia)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="d-flex align-items-center" style="gap: 0.5em;">
                            @php
                                $trofeo = '';
                                if ($i === 0) {
                                    $trofeo = '🏆';
                                } elseif ($i === 1) {
                                    $trofeo = '🥈';
                                } elseif ($i === 2) {
                                    $trofeo = '🥉';
                                }
                            @endphp
                            @if ($trofeo)
                                <span style="font-size: 1.3em;">{{ $trofeo }}</span>
                            @endif
                            {{ $academia->nombreAcademia ?? 'Sin nombre' }}
                            @if (!empty($academia->ubicacionAcademia))
                                <small class="text-muted ms-2">- {{ $academia->ubicacionAcademia }}</small>
                            @endif
                        </span>
                        <span class="badge bg-success rounded-pill d-flex align-items-center" style="gap: 0.3em;">
                            {{ $academia->total_alumnos ?? '0' }}
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="gold"
                                class="bi bi-people-fill ms-1" viewBox="0 0 16 16">
                                <path
                                    d="M13 7a3 3 0 1 0-6 0 3 3 0 0 0 6 0zm-7 3a2 2 0 1 0-4 0 2 2 0 0 0 4 0zm9 2c0-1-1-2-3-2s-3 1-3 2v1h6v-1zm-7 1v-1c0-.628.134-1.197.356-1.684C2.67 10.07 1 11.07 1 12v1h5z" />
                            </svg>
                        </span>
                    </li>
                @endforeach
            </ol>
        </div>
    @endif

</div>

</div>


@if (isset($_GET['toastrErr']))
    <script>
        let toastrMsg = `{{ $_GET['toastrErr'] }}`;
    </script>
@endif

<script>
    const defecto = `{{ RUTA_URL }}` + "/public/img/default_profile.png";
    const RUTA_ACADEMIA = `{{ RUTA_URL }}` + "/academia";
    const gimnasios = @json($academias);
</script>

<script type="module" src="{{ RUTA_URL }}/public/js/home.js"></script>


@include('includes.footer')
