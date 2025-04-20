@include('includes.header')

<?php
// Decodificamos la cadena JSON almacenada en la sesión
$usuario = json_decode($_SESSION['userLogin']['usuario']);
$userRole = $usuario->rol;

?>


<h2>Solicitud de acceso a la academia</h2>
<h1><strong>{{ $academia->nombreAcademia }}</strong></h1>

<form action="{{ RUTA_URL }}/academia/solicitarAcceso" method="POST">
    @csrf
    <input type="hidden" name="idAcademia" value="{{ $academia->idAcademia }}">
    <input type="hidden" name="idUsuario" value="{{ $usuario->idUsuario }}">
    <button type="submit" class="btn btn-primary">Crear Solicitud</button>
</form>



@include('includes.footer')
