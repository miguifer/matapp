// Ocultar navegación si existe
if (document.querySelector('#navegacion')) {
    document.querySelector('#navegacion').style.display = 'none';
}

// Tabs
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

// Gráfico de alumnos por modalidad
const ctx = document.getElementById('miGrafico').getContext('2d');
const estadisticasAcademia = window.estadisticaAcademia;
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
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            }
        }
    }
});

// Gráfico de academias por modalidad
const estadisticaAcademiaModalidad = window.estadisticaAcademiaModalidad;
const labelsModalidad = estadisticaAcademiaModalidad.map(item => item.modalidad);
const dataModalidad = estadisticaAcademiaModalidad.map(item => item.numAcademias);
const palette = [
    '#8dd3c7', '#ffffb3', '#bebada', '#fb8072', '#80b1d3',
    '#fdb462', '#b3de69', '#fccde5', '#d9d9d9', '#bc80bd',
    '#ccebc5', '#ffed6f'
];
const backgroundColors = labelsModalidad.map((_, i) => palette[i % palette.length]);
const ctxAcademiasModalidad = document.getElementById('graficoAcademias').getContext('2d');
const graficoAcademiasModalidad = new Chart(ctxAcademiasModalidad, {
    type: 'pie',
    data: {
        labels: labelsModalidad,
        datasets: [{
            label: 'Número de academias',
            data: dataModalidad,
            backgroundColor: backgroundColors,
            borderColor: '#111827',
            borderWidth: 1
        }]
    },
    options: {
        plugins: {
            legend: {
                display: true
            }
        }
    }
});

// DataTables y eventos de tarjetas
$(document).ready(function () {
    $('#tablaUsuarios').DataTable();
    $('#tablaAcademias').DataTable();

    document.getElementById('card-total-usuarios').addEventListener('click', function () {
        document.getElementById('usuarios-tab').click();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    document.getElementById('card-total-academias').addEventListener('click', function () {
        document.getElementById('academias-tab').click();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Modal histórico de clases
    $('.fila-academia').on('click', function () {
        const idAcademia = $(this).data('id');
        $.ajax({
            url: window.RUTA_URL + '/admin/historicoClases/' + idAcademia,
            method: 'GET',
            success: function (res) {
                let clases = [];
                try {
                    clases = typeof res === 'string' ? JSON.parse(res) : res;
                } catch (e) {
                    clases = [];
                }
                let html = '';
                if (clases.length > 0) {
                    clases.forEach(function (clase) {
                        html += `<tr>
<td>${clase.start}</td>
<td>${clase.end ? clase.end : ''}</td> 
<td>${clase.title}</td>
<td>${clase.entrenador ?? ''}</td>
<td>
    <button class="btn btn-outline-primary btn-sm ver-participantes" data-id="${clase.id}">
        <i class="bi bi-people"></i> Ver participantes
    </button>
</td>
</tr>`;
                    });
                } else {
                    html = '<tr><td colspan="5">No hay datos de clases.</td></tr>';
                }
                $('#tablaHistorico tbody').html(html);
                $('#modalHistorico').modal('show');
            },
            error: function () {
                $('#tablaHistorico tbody').html(
                    '<tr><td colspan="4">Error al cargar los datos.</td></tr>');
                $('#modalHistorico').modal('show');
            }
        });
    });

    $('#modalHistorico .btn-close').on('click', function () {
        $('#modalHistorico').modal('hide');
    });

    // Participantes de clase
    $(document).on('click', '.ver-participantes', function () {
        const idClase = $(this).data('id');
        $.ajax({
            url: window.RUTA_URL + '/admin/participantesClase/' + idClase,
            method: 'GET',
            success: function (res) {
                let participantes = [];
                try {
                    participantes = typeof res === 'string' ? JSON.parse(res) : res;
                } catch (e) {
                    participantes = [];
                }
                let html = '';
                if (participantes.length > 0) {
                    participantes.forEach(function (p) {
                        html += `<tr>
<td>${p.login ?? ''}</td>
<td>${p.nombreUsuario ?? p.nombre ?? ''}</td>
<td>${p.asistencia ? 'Sí' : 'No'}</td>
</tr>`;
                    });
                } else {
                    html = '<tr><td colspan="3">No hay participantes.</td></tr>';
                }
                $('#tablaParticipantes tbody').html(html);
                $('#modalParticipantes').modal('show');
            },
            error: function () {
                $('#tablaParticipantes tbody').html('<tr><td colspan="3">Error al cargar los participantes.</td></tr>');
                $('#modalParticipantes').modal('show');
            }
        });
    });

    $('#modalParticipantes .btn-close').on('click', function () {
        $('#modalParticipantes').modal('hide');
    });
});