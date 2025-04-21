@include('includes.header')


@php

    $usuario = json_decode($_SESSION['userLogin']['usuario']);
    $loginUsuario = $usuario->login; // o el campo que uses como nombre
    $rolUsuario = $usuario->rol; // o el campo que uses como nombre
    $nombreUsuario = $usuario->nombreUsuario;

@endphp

<h1><strong>{{ $nombreUsuario }}</strong></h1>

<h1 class="text-center tituloCalendario">Panel de administrador</h1>

<h4>Estadísticas de tipos de Academias</h4>
<canvas id="miGrafico"></canvas>


<?php
// Asegúrate de que la variable $estadisticaAcademia tiene los datos correctos
$estadisticaAcademiaJS = json_encode($estadisticaAcademia);
?>

<script>
    const ctx = document.getElementById('miGrafico').getContext('2d');

    // Pasamos los datos de PHP a JavaScript sin comillas adicionales
    const estadisticasAcademia = <?= $estadisticaAcademiaJS ?>;

    // Extraemos los nombres de los tipos de academia y los números de alumnos
    const labels = estadisticasAcademia.map(item => item.nombreTipo);
    const data = estadisticasAcademia.map(item => item.numAlumnos);

    // Crear el gráfico con los datos obtenidos
    const miGrafico = new Chart(ctx, {
        type: 'doughnut', // tipos: bar, line, pie, doughnut, radar, polarArea...
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
