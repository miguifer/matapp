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

            $this->blade = new BladeOne($this->views, $this->cache, BladeOne::MODE_AUTO);
            echo $this->blade->run("perfil.inicio", []);
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

                    if ($usuario->email == $_POST['email']) {
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


            if (empty($errores)) {

                if ($this->academiaModelo->modificarUsuario($id, $datos)) {

                    unset($_SESSION['userLogin']);

                    if (isset($usuario->imagen)) {
                        $usuario->imagen = base64_encode($usuario->imagen);
                    }

                    $_SESSION['userLogin'] = [
                        'usuario' => json_encode($usuario),
                    ];



                    redireccionar('/usuario/perfil');
                } else {

                    $datos['error_modificacion'] = "Hubo un error al modificar el cliente.";
                    $this->vista('usuario/perfil', $datos);
                }
            } else {

                $datos['errores'] = $errores;
                $this->vista('usuario/perfil', $datos);
            }
        } else {

            redireccionar('/usuario/perfil');
        }
    }


    public function actualizarImagen()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $errores = [];
            $id = test_input($_POST['id']);
            $usuario = $this->academiaModelo->getUsuarioPorId($id);

            // Verificar si se sube una imagen
            if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == 0) {
                $permitidos = ['image/jpeg', 'image/png', 'image/gif'];
                $max_tamano = 2 * 1024 * 1024; // 2MB

                $tipo = mime_content_type($_FILES["imagen"]["tmp_name"]);
                $tamano = $_FILES["imagen"]["size"];

                if (!in_array($tipo, $permitidos)) {
                    $errores['imagen_error'] = "Formato de imagen no permitido. Solo JPG, PNG o GIF.";
                    $datos['imagen'] = $usuario->imagen;
                } elseif ($tamano > $max_tamano) {
                    $errores['imagen_error'] = "La imagen es demasiado grande. Máximo 2MB.";
                    $datos['imagen'] = $usuario->imagen;
                } else {
                    // Obtener los datos binarios de la imagen
                    $imagen = file_get_contents($_FILES["imagen"]["tmp_name"]);
                    $datos['imagen'] = $imagen;
                }
            } else {
                $errores['imagen_error'] = "Error al subir la imagen.";
                $datos['imagen'] = $usuario->imagen; // Mantener la imagen actual si hay un error
            }

            // Si no hay errores, actualizar la imagen en la base de datos
            if (empty($errores)) {
                if ($this->academiaModelo->modificarImagen($id, $datos)) {
                    // Actualizar la imagen en la sesión

                    //tocho usuarios
                    $usuario = $this->academiaModelo->getUsuarioPorId($id);

                    $usuario->imagen = base64_encode($usuario->imagen);

                    $usuario->rol = $this->academiaModelo->obtenerRolDeUsuario($usuario->idUsuario) ?? 'Cliente';

                    // Ahora sí puedes codificar todo
                    $_SESSION['userLogin'] = [
                        'usuario' => json_encode($usuario),
                    ];

                    // tocho usuarios

                    // Redirigir a la página del perfil
                    redireccionar('/perfil');
                } else {
                    // En caso de error al modificar la imagen
                    $datos['error_modificacion'] = "Hubo un error al modificar la imagen.";

                    $this->blade = new BladeOne($this->views, $this->cache, BladeOne::MODE_AUTO);
                    echo $this->blade->run("perfil.inicio", $datos);
                }
            } else {
                // Si hay errores, mostrar el formulario de perfil con los errores
                $datos['errores'] = $errores;
                $this->blade = new BladeOne($this->views, $this->cache, BladeOne::MODE_AUTO);
                echo $this->blade->run("perfil.inicio", $datos);
            }
        } else {
            // Si no es un POST, redirigir a la página del perfil
            redireccionar('/');
        }
    }
}
