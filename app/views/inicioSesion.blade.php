@include('includes.header')


{{-- <div class="container">
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
</div> --}}

<link rel="stylesheet" type="text/css" href="<?= RUTA_URL ?>/public/css/inicioSesion.css">

</div>

<div class="container-fluid">

    <div class="row">
        <div class="col-12 col-md-6 text-white d-none d-md-block" id="izquierda">
            <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="<?= RUTA_URL ?>/public/img/registro/i1.jpg" class="d-block w-100" alt="..." />
                    </div>
                    <div class="carousel-item">
                        <img src="<?= RUTA_URL ?>/public/img/registro/i2.jpg" class="d-block w-100" alt="..." />
                    </div>
                    <div class="carousel-item">
                        <img src="<?= RUTA_URL ?>/public/img/registro/i3.jpg" class="d-block w-100" alt="..." />
                    </div>
                    <div class="carousel-item">
                        <img src="<?= RUTA_URL ?>/public/img/registro/i4.webp" class="d-block w-100" alt="..." />
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>

        <div class="col-12 col-md-6 text-dark bg-white d-flex justify-content-center" style="height: 100vh"
            id="derecha">
            <span class="position-absolute end-0 p-3">¿No tienes una cuenta?
                <a id="link-iniciar-sesion" class="text-decoration-none"
                    href="<?= RUTA_URL ?>/">Registrate →</a></span>

            <div class="register-container w-100 w-md-50 d-flex justify-content-center" style="margin-top: 20vh">
                <form class="p-3 rounded-2" id="form-register" action="<?= RUTA_URL ?>/inicioSesion" method="POST"
                    style="min-width: 70%;">

                    <div class="d-flex justify-content-center mb-4">
                        <img src="<?= RUTA_URL ?>/public/img/favicon/android-chrome-512x512.png" alt="Logo"
                            class="img-fluid" width="50" height="50" id="logoIS" title="Home" />
                    </div>

                    <h4>Inicia Sesión en MatApp</h4>



                    <div>
                        <label for="login" class="fw-semibold form-label mt-4">Nombre de usuario</label><sup>*</sup>
                        <input type="text" class="form-control mb-2" placeholder="Nombre de usuario" id="login"
                            name="login" value="" />
                    </div>
                    <div>
                        <label for="password" class="fw-semibold form-label mt-2">Contraseña</label><sup>*</sup>
                        <input type="password" class="form-control" placeholder="Contraseña" id="password"
                            name="password"
                            value="" />

                    </div>

                    <button type="submit" id="submit" class="mt-4 btn p-2 btn-dark w-100">
                        Iniciar sesión >
                    </button>
                   
                </form>
            </div>
        </div>
    </div>
</div>


<div class="container" id="main">

    <script>
        if (document.querySelector('#navegacion')) {
            document.querySelector('#navegacion').style.display = 'none';
        }
    </script>

    

    @include('includes.footer')
