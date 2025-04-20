@include('includes.header')


<div class="container">
    <h2>Inicio de Sesión</h2>
    <form action="{{ RUTA_URL }}/inicioSesion/" method="POST">
        @csrf
        <div class="form-group">
            <label for="login">login:</label>
            <input type="text" class="form-control" id="login" name="login" required>
        </div>
        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
    </form>
</div>

@include('includes.footer')
