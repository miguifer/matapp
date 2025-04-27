@include('includes.header')


<link rel="stylesheet" type="text/css" href="<?= RUTA_URL ?>/public/css/registroUsuario.css">

</div>

<div class="container-fluid">

    <div class="row">


        <div class="col-12 col-md-6 text-dark bg-white d-flex justify-content-center" style="height: 100vh" id="derecha">
            <span class="position-absolute start-0 p-3">¿Ya tienes una cuenta?
                <a id="link-iniciar-sesion" class="text-decoration-none" href="<?= RUTA_URL ?>/inicioSesion">Inicia
                    Sesion →</a></span>


            <div class="register-container w-100 w-md-50 d-flex justify-content-center" style="margin-top: 15vh">

                <?php
                
                if (isset($_GET['errores']) && !empty($_GET['errores'])) {
                    $errores = json_decode(urldecode($_GET['errores']));
                }
                
                if (isset($_GET['success']) && !empty($_GET['success'])) {
                    $success = urldecode($_GET['success']);
                }
                
                if (isset($_GET['registro_error']) && !empty($_GET['registro_error'])) {
                    $registro_error = urldecode($_GET['registro_error']);
                }
                
                if (isset($_GET['login']) && !empty($_GET['login'])) {
                    $login = urldecode($_GET['login']);
                }
                
                if (isset($_GET['email']) && !empty($_GET['email'])) {
                    $email = urldecode($_GET['email']);
                }
                
                ?>

                <form class="p-3 rounded-2" id="form-register" action="<?= RUTA_URL ?>/registroUsuario" method="POST"
                    style="min-width: 70%;">

                    <div class="d-flex justify-content-center mb-4">
                        <img src="<?= RUTA_URL ?>/public/img/favicon/android-chrome-512x512.png" alt="Logo"
                            class="img-fluid" width="50" height="50" id="logoIS" title="Home" />
                    </div>

                    <h4>Registrate en MatApp</h4>



                    <div>
                        <label for="email" class="fw-semibold form-label mt-4">Email</label><sup>*</sup>
                        <input type="email" class="form-control mb-2" placeholder="Email" id="email"
                            name="email" value="<?= isset($email) ? $email : '' ?>" />

                        <?php

                        if (isset($errores->email_error)) {

                        ?>

                        <span id="error_email" class="small text-danger d-flex align-items-center"><svg
                                xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="-2 -3 24 24"
                                class="ms-1 me-1">
                                <path fill="currentColor"
                                    d="m12.8 1.613l6.701 11.161c.963 1.603.49 3.712-1.057 4.71a3.2 3.2 0 0 1-1.743.516H3.298C1.477 18 0 16.47 0 14.581c0-.639.173-1.264.498-1.807L7.2 1.613C8.162.01 10.196-.481 11.743.517c.428.276.79.651 1.057 1.096M10 14a1 1 0 1 0 0-2a1 1 0 0 0 0 2m0-9a1 1 0 0 0-1 1v4a1 1 0 0 0 2 0V6a1 1 0 0 0-1-1" />
                            </svg><?= $errores->email_error ?></span>

                        <?php
                        }
                        ?>
                    </div>
                    <div>
                        <label for="login" class="fw-semibold form-label">Nombre de usuario</label><sup>*</sup>
                        <input type="text" class="form-control mb-2" placeholder="Nombre de usuario" id="login"
                            name="login" value="<?= isset($login) ? $login : '' ?>" />
                        <?php

                        if (isset($errores->login_error)) {

                        ?>
                        <span id="error_login" class="small text-danger d-flex align-items-center"><svg
                                xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="-2 -3 24 24"
                                class="ms-1 me-1">
                                <path fill="currentColor"
                                    d="m12.8 1.613l6.701 11.161c.963 1.603.49 3.712-1.057 4.71a3.2 3.2 0 0 1-1.743.516H3.298C1.477 18 0 16.47 0 14.581c0-.639.173-1.264.498-1.807L7.2 1.613C8.162.01 10.196-.481 11.743.517c.428.276.79.651 1.057 1.096M10 14a1 1 0 1 0 0-2a1 1 0 0 0 0 2m0-9a1 1 0 0 0-1 1v4a1 1 0 0 0 2 0V6a1 1 0 0 0-1-1" />
                            </svg><?= $errores->login_error ?></span>
                        <?php
                        }
                        ?>
                    </div>
                    <div>
                        <label for="password" class="fw-semibold form-label mt-2">Contraseña</label><sup>*</sup>
                        <input type="password" class="form-control" placeholder="Contraseña" id="password"
                            name="password" />

                        <input type="password" class="form-control mt-2" placeholder="Confirmar contraseña"
                            id="password" name="password2" />
                        <?php

                        if (isset($errores->password_error)) {

                        ?>
                        <span id="error_password" class="small text-danger d-flex align-items-center"><svg
                                xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="-2 -3 24 24"
                                class="ms-1 me-1">
                                <path fill="currentColor"
                                    d="m12.8 1.613l6.701 11.161c.963 1.603.49 3.712-1.057 4.71a3.2 3.2 0 0 1-1.743.516H3.298C1.477 18 0 16.47 0 14.581c0-.639.173-1.264.498-1.807L7.2 1.613C8.162.01 10.196-.481 11.743.517c.428.276.79.651 1.057 1.096M10 14a1 1 0 1 0 0-2a1 1 0 0 0 0 2m0-9a1 1 0 0 0-1 1v4a1 1 0 0 0 2 0V6a1 1 0 0 0-1-1" />
                            </svg><?= $errores->password_error ?></span>
                        <?php
                        }
                        ?>
                    </div>

                    <button type="submit" id="submit" class="mt-4 btn p-2 btn-dark w-100">
                        Registrarse >
                    </button>

                    <?php

                    if (isset($registro_error)) {

                    ?>
                    <span id="error_password" class="small text-danger d-flex align-items-center"><svg
                            xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="-2 -3 24 24"
                            class="ms-1 me-1">
                            <path fill="currentColor"
                                d="m12.8 1.613l6.701 11.161c.963 1.603.49 3.712-1.057 4.71a3.2 3.2 0 0 1-1.743.516H3.298C1.477 18 0 16.47 0 14.581c0-.639.173-1.264.498-1.807L7.2 1.613C8.162.01 10.196-.481 11.743.517c.428.276.79.651 1.057 1.096M10 14a1 1 0 1 0 0-2a1 1 0 0 0 0 2m0-9a1 1 0 0 0-1 1v4a1 1 0 0 0 2 0V6a1 1 0 0 0-1-1" />
                        </svg><?= $registro_error ?></span>
                    <?php
                    }

                    if (isset($success)) {

                    ?>
                    <p class="alert alert-success mt-2"><?= $success ?></p>
                    <?php
                    } else if (isset($no_success)) {
                    ?>
                    <p class="alert alert-danger mt-2"><?= $no_success ?></p>
                    <?php
                    }
                    ?>

                </form>
            </div>
        </div>

        <div class="col-12 col-md-6 text-white d-none d-md-block" id="izquierda">
            <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="<?= RUTA_URL ?>/public/img/registro/i2.jpg" class="d-block w-100" alt="..." />
                    </div>
                    <div class="carousel-item">
                        <img src="<?= RUTA_URL ?>/public/img/registro/i1.jpg" class="d-block w-100" alt="..." />
                    </div>
                    <div class="carousel-item">
                        <img src="<?= RUTA_URL ?>/public/img/registro/i3.jpg" class="d-block w-100" alt="..." />
                    </div>
                    <div class="carousel-item">
                        <img src="<?= RUTA_URL ?>/public/img/registro/i4.png" class="d-block w-100" alt="..." />
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

    </div>
</div>


<div class="container" id="main">

    <script>
        if (document.querySelector('#navegacion')) {
            document.querySelector('#navegacion').style.display = 'none';
        }
    </script>



    @include('includes.footer')
