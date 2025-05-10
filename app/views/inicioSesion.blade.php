@include('includes.header')

<link rel="stylesheet/less" type="text/css" href="<?= RUTA_URL ?>/public/css/styles/matapp-inicioSesion.less">

</div>

<div class="container-fluid">

    <div class="row">
        <div class="col-12 col-md-6 text-white d-none d-md-block" id="izquierda">
            <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="<?= RUTA_URL ?>/public/img/inicioSesion/i1.jpg" class="d-block w-100"
                            alt="..." />
                    </div>
                    <div class="carousel-item">
                        <img src="<?= RUTA_URL ?>/public/img/inicioSesion/i2.jpg" class="d-block w-100"
                            alt="..." />
                    </div>
                    <div class="carousel-item">
                        <img src="<?= RUTA_URL ?>/public/img/inicioSesion/i3.jpg" class="d-block w-100"
                            alt="..." />
                    </div>
                    <div class="carousel-item">
                        <img src="<?= RUTA_URL ?>/public/img/inicioSesion/i4.webp" class="d-block w-100"
                            alt="..." />
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
                    href="<?= RUTA_URL ?>/registroUsuario">Registrate →</a></span>


            <div class="register-container w-100 w-md-50 d-flex justify-content-center" style="margin-top: 20vh">



                <form class="p-3 rounded-2" id="form-register"
                    action="<?= RUTA_URL ?>/inicioSesion<?= isset($_GET['academia']) ? '?academia=' . urlencode($_GET['academia']) : '' ?>"
                    method="POST" style="min-width: 70%;">

                    <div class="d-flex justify-content-center mb-4">
                        <a href="<?= RUTA_URL ?>/">
                            <img src="<?= RUTA_URL ?>/public/img/favicon/android-chrome-512x512.png" alt="Logo"
                                class="img-fluid" width="50" height="50" id="logoIS" title="Home" />
                        </a>
                    </div>

                    <h4>Inicia Sesión en MatApp</h4>

                    <?php
                    if (isset($_GET['error'])) {
                    
                    ?>

                    <span id="error_email" class="small text-danger d-flex align-items-center"><svg
                            xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="-2 -3 24 24"
                            class="ms-1 me-1">
                            <path fill="currentColor"
                                d="m12.8 1.613l6.701 11.161c.963 1.603.49 3.712-1.057 4.71a3.2 3.2 0 0 1-1.743.516H3.298C1.477 18 0 16.47 0 14.581c0-.639.173-1.264.498-1.807L7.2 1.613C8.162.01 10.196-.481 11.743.517c.428.276.79.651 1.057 1.096M10 14a1 1 0 1 0 0-2a1 1 0 0 0 0 2m0-9a1 1 0 0 0-1 1v4a1 1 0 0 0 2 0V6a1 1 0 0 0-1-1" />
                        </svg><?= str_replace('ç', ' ', $_GET['error']) ?></span>
                    <?php
                    }
                    ?>


                    <div>
                        <label for="login" class="fw-semibold form-label mt-4">Nombre de usuario</label><sup>*</sup>
                        <input type="text" class="form-control mb-2" placeholder="Nombre de usuario" id="login"
                            name="login" value="" />
                    </div>
                    <div>
                        <label for="password" class="fw-semibold form-label mt-2">Contraseña</label><sup>*</sup>
                        <input type="password" class="form-control" placeholder="Contraseña" id="password"
                            name="password" value="" />

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


    <script src="<?= RUTA_URL ?>/public/js/navDisplay.js"></script>


    @include('includes.footer')
