<?php

use eftec\bladeone\BladeOne;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();
class perfil extends Controlador
{

    private $academiaModelo;
    private $blade;
    private $views = __DIR__ . '/../views';
    private $cache = __DIR__ . '/../cache';

    public function __construct()
    {
        $this->academiaModelo = $this->modelo('academiaModelo');
    }

    public function index()
    {
        if (isset($_SESSION['userLogin'])) {

            $usuario = isset($_SESSION['userLogin']['usuario']) ? json_decode($_SESSION['userLogin']['usuario']) : null;
            $datos['solicitudesS'] = $this->academiaModelo->getSolicitudesPorIdUsuario($usuario->idUsuario);;
            $datos['asistencias'] = $this->academiaModelo->getAsistenciaPorIdUsuario($usuario->idUsuario);
            $datos['academias'] = $this->academiaModelo->getAcademiasPorIdUsuario($usuario->idUsuario);
            
            $this->blade = new BladeOne($this->views, $this->cache, BladeOne::MODE_AUTO);
            echo $this->blade->run("perfil.inicio", $datos);
        } else {
            redireccionar('/');
        }
    }

    public function actualizarPerfil()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $errores = [];

            $id = test_input($_POST['id']);
            $usuario = $this->academiaModelo->getUsuarioPorId($id);

            if (isset($_POST["login"])) {
                if (!empty($_POST['login'])) {

                    $login = test_input($_POST['login']);

                    if ($usuario->login == $_POST['login']) {
                        $datos['login'] = $login;
                    } else {

                        if ($this->academiaModelo->obtenerUsuarioPorLogin($login)) {
                            $login_error = "Este login ya ha sido introducido";
                            $errores['login_error'] = $login_error;
                            $datos['login'] = "";
                        } else {

                            if (strlen($login) < 50) {
                                $datos['login'] = $login;
                            } else {
                                $login_error = "El login es demasiado largo";
                                $errores['login_error'] = $login_error;
                                $datos['login'] = "";
                            }
                        }
                    }
                } else {
                    $login_error = "El login es obligatorio";
                    $errores['login_error'] = $login_error;
                    $datos['login'] = "";
                }
            } else {
                $login_error = "El login es obligatorio";
                $errores['login_error'] = $login_error;
                $datos['login'] = "";
            }

            if (isset($_POST["email"])) {
                if (!empty($_POST['email'])) {

                    $email = test_input($_POST['email']);

                    if ($usuario->emailUsuario == $_POST['email']) {
                        $datos['email'] = $email;
                    } else {
                        if ($this->academiaModelo->getUsuarioPorEmail($email)) {

                            $email_error = "Este email ya ha sido introducido";
                            $errores['email_error'] = $email_error;
                            $datos['email'] = "";
                        } else {

                            if (validar_email($_POST['email'])) {
                                $datos['email'] = $email;
                            } else {
                                $email_error = "Formato de email inválido";
                                $errores['email_error'] = $email_error;
                                $datos['email'] = "";
                            }
                        }
                    }
                } else {
                    $email_error = "El email es obligatorio";
                    $errores['email_error'] = $email_error;
                    $datos['email'] = "";
                }
            } else {
                $email_error = "El email es obligatorio";
                $errores['email_error'] = $email_error;
                $datos['email'] = "";
            }

            if (isset($_POST["nombreUsuario"])) {
                $nombreUsuario = test_input($_POST['nombreUsuario']);
                if (strlen($nombreUsuario) <= 50) {
                    $datos['nombreUsuario'] = $nombreUsuario;
                } else {
                    $errores['nombreUsuario_error'] = "El nombre no puede superar los 50 caracteres.";
                    $datos['nombreUsuario'] = "";
                }
            } else {
                $datos['nombreUsuario'] = "";
            }

            if (isset($_POST["apellido1Usuario"])) {
                $apellido1Usuario = test_input($_POST['apellido1Usuario']);
                if (strlen($apellido1Usuario) <= 50) {
                    $datos['apellido1Usuario'] = $apellido1Usuario;
                } else {
                    $errores['apellido1Usuario_error'] = "El primer apellido no puede superar los 50 caracteres.";
                    $datos['apellido1Usuario'] = "";
                }
            } else {
                $datos['apellido1Usuario'] = "";
            }

            if (isset($_POST["apellido2Usuario"])) {
                $apellido2Usuario = test_input($_POST['apellido2Usuario']);
                if (strlen($apellido2Usuario) <= 50) {
                    $datos['apellido2Usuario'] = $apellido2Usuario;
                } else {
                    $errores['apellido2Usuario_error'] = "El segundo apellido no puede superar los 50 caracteres.";
                    $datos['apellido2Usuario'] = "";
                }
            } else {
                $datos['apellido2Usuario'] = "";
            }

            if (isset($_POST["telefonoUsuario"])) {
                $telefonoUsuario = test_input($_POST['telefonoUsuario']);
                if (!empty($telefonoUsuario)) {
                    if (validar_telefono($telefonoUsuario)) {
                        $datos['telefonoUsuario'] = $telefonoUsuario;
                    } else {
                        $errores['telefono_error'] = "El teléfono debe tener 9 dígitos numéricos.";
                        $datos['telefonoUsuario'] = "";
                    }
                } else {
                    $datos['telefonoUsuario'] = "";
                }
            } else {
                $datos['telefonoUsuario'] = "";
            }

            if (isset($_POST["password"]) && !empty($_POST["password"])) {
                $password = $_POST["password"];
                if (strlen($password) < 6) {
                    $errores['password_error'] = "La contraseña debe tener al menos 6 caracteres.";
                    $datos['password'] = "";
                } else {
                    $datos['password'] = password_hash($password, PASSWORD_DEFAULT);
                }
            } else {
                $datos['password'] = null;
            }

            if (empty($errores)) {
                if ($datos['password'] === null) {
                    unset($datos['password']);
                }
                if ($this->academiaModelo->modificarUsuario($id, $datos)) {

                    unset($_SESSION['userLogin']);

                    $usuario = $this->academiaModelo->getUsuarioPorId($id);

                    $usuario->imagen = base64_encode($usuario->imagen);

                    $usuario->rol = $this->academiaModelo->obtenerRolDeUsuario($usuario->idUsuario) ?? 'Cliente';

                    $_SESSION['userLogin'] = [
                        'usuario' => json_encode($usuario),
                    ];

                    redireccionar('/perfil?toastrMsg=Perfil actualizado correctamente');
                } else {

                    $datos['error_modificacion'] = "Hubo un error al modificar el cliente.";
                    $this->blade = new BladeOne($this->views, $this->cache, BladeOne::MODE_AUTO);
                    echo $this->blade->run("perfil.inicio", $datos);
                }
            } else {

                $datos['errores'] = $errores;
                $this->blade = new BladeOne($this->views, $this->cache, BladeOne::MODE_AUTO);
                echo $this->blade->run("perfil.inicio", $datos);
            }
        } else {

            redireccionar('/');
        }
    }

    public function actualizarImagen()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $errores = [];
            $id = test_input($_POST['id']);
            $usuario = $this->academiaModelo->getUsuarioPorId($id);

            if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == 0) {
                $permitidos = ['image/jpeg', 'image/png', 'image/gif'];
                $max_tamano = 2 * 1024 * 1024;

                $tipo = mime_content_type($_FILES["imagen"]["tmp_name"]);
                $tamano = $_FILES["imagen"]["size"];

                if (!in_array($tipo, $permitidos)) {
                    $errores['imagen_error'] = "Formato de imagen no permitido. Solo JPG, PNG o GIF.";
                    $datos['imagen'] = $usuario->imagen;
                } elseif ($tamano > $max_tamano) {
                    $errores['imagen_error'] = "La imagen es demasiado grande. Máximo 2MB.";
                    $datos['imagen'] = $usuario->imagen;
                } else {
                    $imagen = file_get_contents($_FILES["imagen"]["tmp_name"]);
                    $datos['imagen'] = $imagen;
                }
            } else {
                $errores['imagen_error'] = "Error al subir la imagen.";
                $datos['imagen'] = $usuario->imagen;
            }

            if (empty($errores)) {
                if ($this->academiaModelo->modificarImagen($id, $datos)) {

                    $usuario = $this->academiaModelo->getUsuarioPorId($id);

                    $usuario->imagen = base64_encode($usuario->imagen);

                    $usuario->rol = $this->academiaModelo->obtenerRolDeUsuario($usuario->idUsuario) ?? 'Cliente';

                    $_SESSION['userLogin'] = [
                        'usuario' => json_encode($usuario),
                    ];

                    redireccionar('/perfil?toastrMsg=Imagen de perfil actualizada correctamente');
                } else {
                    $datos['error_modificacion'] = "Hubo un error al modificar la imagen.";

                    $this->blade = new BladeOne($this->views, $this->cache, BladeOne::MODE_AUTO);
                    echo $this->blade->run("perfil.inicio", $datos);
                }
            } else {
                $datos['errores'] = $errores;
                $this->blade = new BladeOne($this->views, $this->cache, BladeOne::MODE_AUTO);
                echo $this->blade->run("perfil.inicio", $datos);
            }
        } else {
            redireccionar('/');
        }
    }
}