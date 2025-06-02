<?php

// Cargar clases de BladeOne 
use eftec\bladeone\BladeOne;

// Controlador para el inicio de sesión
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


    public function index()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            // Validar y limpiar los datos de entrada
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

            // Si no hay errores, proceder con el inicio de sesión
            if (!isset($datos['error'])) {

                if ($this->academiaModelo->obtenerUsuarioPorLogin($login)) {

                    $usuario = $this->academiaModelo->obtenerUsuarioPorLogin($login);

                    // Verificar si el usuario está activo 
                    if ($usuario->activo == 1) {

                        // Verificar la contraseña hasheada
                        if (password_verify($password, $usuario->password)) {

                            // En caso de no tener un rol asignado, asignar 'Cliente'
                            $usuario->rol = $this->academiaModelo->obtenerRolDeUsuario($usuario->idUsuario) ?? 'Cliente';

                            // Hacer que se vea la cuenta en línea
                            $this->academiaModelo->actualizarActividad($usuario->idUsuario);

                            // Iniciar sesión
                            if (isset($usuario->imagen)) {
                                $usuario->imagen = base64_encode($usuario->imagen);
                            }
                            $_SESSION['userLogin'] = [
                                'usuario' => json_encode($usuario),
                            ];
                            $this->logSession($login, true, 'Login correcto');

                            // Si venía de una academia, redirigir a esa academia
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

    /**
     * Cierra la sesión del usuario y redirige a la página de inicio.
     */
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

    /**
     * Registra un intento de inicio de sesión en un archivo de log.
     *
     * @param string $login El nombre de usuario que intentó iniciar sesión.
     * @param bool $success Indica si el inicio de sesión fue exitoso o no.
     * @param string $reason Razón del éxito o fallo del inicio de sesión.
     */
    private function logSession($login, $success, $reason = '')
    {
        $file = __DIR__ . '/../logs/sessions.log';
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
