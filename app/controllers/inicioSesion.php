<?php


use eftec\bladeone\BladeOne;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();
class inicioSesion extends Controlador
{

    private $academiaModelo;
    private $blade;
    private $views = __DIR__ . '/../views';
    private $cache = __DIR__ . '/../cache';

    public function __construct()
    {
        $this->academiaModelo = $this->modelo('academiaModelo');
    }

    // public function index()
    // {
    //     if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //         $login = $_POST['login'];
    //         $password = $_POST['password'];

    //         if ($this->academiaModelo->obtenerUsuarioPorLogin($login)) {

    //             $usuario = $this->academiaModelo->obtenerUsuarioPorLogin($login);

    //             if (password_verify($password, $usuario->password)) {

    //                 //usuarios base no estan en roles, asi que si no eisten en la tabla dará null
    //                 $usuario->rol = $this->academiaModelo->obtenerRolDeUsuario($usuario->idUsuario) ?? 'Cliente';

    //                 //aqui se guarda el usuario en la sesiiony puedo añadir mas cosas

    //                 $_SESSION['userLogin'] = [
    //                     'usuario' => json_encode($usuario),
    //                 ];

    //                 redireccionar('/');
    //             } else {
    //                 redireccionar('/');
    //             }
    //         } else {
    //             redireccionar('/');
    //         }
    //     } else if (isset($_SESSION['userLogin'])) {
    //         redireccionar('/');
    //     } else {
    //         $this->blade = new BladeOne($this->views, $this->cache, BladeOne::MODE_AUTO);
    //         echo $this->blade->run("inicioSesion", []);
    //     }
    // }

    public function index()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            if (isset($_POST["login"])) {
                if (!empty($_POST['login'])) {

                    if (strlen(($_POST['login'])) < 50) {
                        $login = test_input($_POST["login"]);
                        $datos['login'] = $login;
                    } else {
                        $datos['error'] = "El login no puede tener mas de 50 caracteres";
                    }
                } else {
                    $datos['error'] = "Ambos campos son obligatorios";
                }
            } else {
                $datos['error'] = "Ambos campos son obligatorios";
            }


            if (isset($_POST["password"])) {
                if (!empty($_POST['password'])) {

                    if (strlen(($_POST['password'])) < 50) {
                        $password = test_input($_POST["password"]);
                        $datos['password'] = $password;
                    } else {
                        $datos['error'] = "El password no puede tener mas de 255 caracteres";
                    }
                } else {
                    $datos['error'] = "Ambos campos son obligatorios";
                }
            } else {
                $datos['error'] = "Ambos campos son obligatorios";
            }


            if (!isset($datos['error'])) {


                if ($this->academiaModelo->obtenerUsuarioPorLogin($login)) {


                    $usuario = $this->academiaModelo->obtenerUsuarioPorLogin($login);

                    if ($usuario->activo == 1) {


                        if (password_verify($password, $usuario->password)) {

                            //usuarios base no estan en roles, asi que si no eisten en la tabla dará null
                            $usuario->rol = $this->academiaModelo->obtenerRolDeUsuario($usuario->idUsuario) ?? 'Cliente';

                            $this->academiaModelo->actualizarActividad($usuario->idUsuario);

                            if (isset($usuario->imagen)) {
                                $usuario->imagen = base64_encode($usuario->imagen);
                            }

                            $_SESSION['userLogin'] = [
                                'usuario' => json_encode($usuario),
                            ];

                            // Solo aquí loguea el inicio exitoso
                            $this->logSession($login, true, 'Login correcto');

                            if (isset($_GET['academia'])) {
                                redireccionar('/academia?academia=' . urlencode($_GET['academia']));
                            } else {
                                redireccionar('/');
                            }
                        } else {
                            $this->logSession($login, false, 'Credenciales incorrectas');
                            $datos = [
                                'login' => $login,
                                'password' => $password,
                                'error' => "Credenciales incorrectas"
                            ];
                            // $this->blade = new BladeOne($this->views, $this->cache, BladeOne::MODE_AUTO);
                            // echo $this->blade->run("inicioSesion", $datos);
                            header("Location: " . RUTA_URL . "/inicioSesion?error=Credencialesçincorrectas" .
                                (isset($_GET['academia']) ? "&academia=" . urlencode($_GET['academia']) : ""));
                        }
                    } else {
                        $this->logSession($login, false, 'Cuenta inactiva');
                        $datos = [
                            'login' => $login,
                            'password' => $password,
                            'error' => "Debe activar la cuenta"
                        ];
                        // $this->blade = new BladeOne($this->views, $this->cache, BladeOne::MODE_AUTO);
                        // echo $this->blade->run("inicioSesion", $datos);
                        header("Location: " . RUTA_URL . "/inicioSesion?error=Debeçactivarçlaçcuenta" .
                            (isset($_GET['academia']) ? "&academia=" . urlencode($_GET['academia']) : ""));
                    }
                } else {
                    $this->logSession($login ?? '', false, 'Usuario no encontrado');
                    $datos = [
                        'login' => "",
                        'password' => "",
                        'error' => "Credenciales incorrectas"
                    ];
                    // $this->blade = new BladeOne($this->views, $this->cache, BladeOne::MODE_AUTO);
                    // echo $this->blade->run("inicioSesion", $datos);
                    header("Location: " . RUTA_URL . "/inicioSesion?error=Credencialesçincorrectas" .
                        (isset($_GET['academia']) ? "&academia=" . urlencode($_GET['academia']) : ""));
                }
            } else {
                $this->logSession($login ?? '', false, 'Campos obligatorios vacíos');
                $datos = [
                    'login' => "",
                    'password' => "",
                    'error' => "Ambos campos son obligatorios"
                ];
                // $this->blade = new BladeOne($this->views, $this->cache, BladeOne::MODE_AUTO);
                // echo $this->blade->run("inicioSesion", $datos);
                header("Location: " . RUTA_URL . "/inicioSesion?error=Ambosçcamposçsonçobligatorios" .
                    (isset($_GET['academia']) ? "&academia=" . urlencode($_GET['academia']) : ""));
            }
        } else if (isset($_SESSION['userLogin'])) {
            redireccionar('/');
        } else {
            $this->blade = new BladeOne($this->views, $this->cache, BladeOne::MODE_AUTO);
            echo $this->blade->run("inicioSesion", []);
        }
    }

    public function cerrarSesion()
    {
        $login = '';
        if (isset($_SESSION['userLogin']['usuario'])) {
            $usuario = json_decode($_SESSION['userLogin']['usuario']);
            $login = $usuario->login ?? '';
        }
        $this->logSession($login, true, 'Cierre de sesión');
        unset($_SESSION['userLogin']);
        redireccionar('/');
    }

    private function logSession($login, $success, $reason = '')
    {
        $file = __DIR__ . '/../logs/sessions.log'; // Cambiado para que esté dentro de /app/logs
        if (!file_exists(dirname($file))) {
            mkdir(dirname($file), 0777, true);
        }
        $date = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $status = $success ? 'SUCCESS' : 'FAIL';
        $entry = "[$date][$ip][$login][$status] $reason" . PHP_EOL;
        file_put_contents($file, $entry, FILE_APPEND);
    }
}
