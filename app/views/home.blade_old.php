@include('includes.header')

<table id="academiasTable" class="display compact">
    <thead>
        <tr>
            <th>Nombre Academia</th>
            <th>ID Academia</th>
            <th>Ubicación Academia</th>
            <th>Tipo Academia</th>
            <th>ID Gerente</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($academias as $academia)
            <tr
                onclick="window.location.href='{{ RUTA_URL }}/academia?academia={{ urlencode(json_encode($academia)) }}'">
                <td>{{ $academia->idAcademia }}</td>
                <td>{{ $academia->nombreAcademia }}</td>
                <td>{{ $academia->ubicacacionAcademia }}</td>
                <td>{{ $academia->tipoAcademia }}</td>
                <td>{{ $academia->idGerente }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<script>
    $(document).ready(function() {
        $('#academiasTable').DataTable();
    });
</script>

<style>
    #academiasTable tbody tr {
        cursor: pointer;
    }
</style>

<script>
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000"
    };

    // toastr.info('Ya tienes una solicitud activa para esta academia.');
</script>


@include('includes.footer')
