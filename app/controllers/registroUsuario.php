<?php

// Cargar clases de BladeOne, Dotenv y PHPMailer
use eftec\bladeone\BladeOne;
use Dotenv\Dotenv;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

// Controlador para el registro de usuarios
class registroUsuario extends Controlador
{

    private $academiaModelo;
    private $blade;
    private $views = __DIR__ . '/../views';
    private $cache = __DIR__ . '/../cache';

    public function __construct()
    {
        $this->academiaModelo = $this->modelo('academiaModelo');
    }

    /**
     * Envia un correo de confirmación al usuario después del registro.
     *
     * @param string $email El email del usuario.
     * @param string $login El login del usuario.
     * @return bool Devuelve true si el correo se envió correctamente, false en caso contrario.
     */
    public function enviarCorreo($email, $login)
    {

        // Obtiene datos del usuario creado 
        $usuario = $this->academiaModelo->obtenerUsuarioPorLogin($login);
        $token = $usuario->token;
        $id = $usuario->idUsuario;

        // URl con el token y el id y token del usuario para confirmar la cuenta
        $URL_CONFIRMACION = RUTA_URL . "/registroUsuario/autenticar?id=" . $id . "&token=" . $token;

        // Iniciar PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Credenciales / Conf de servidor de correo y cuenta
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['EMAIL_EMAIL']; // Dirección de correo electrónico
            $mail->Password = str_replace("_", " ", $_ENV['EMAIL_CONTRASENA_APLICACION']); // Contraseña de aplicación
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom($_ENV['EMAIL_EMAIL'], NOMBRE_SITIO);
            $mail->addAddress($email, $login);

            // Correo
            $mail->Subject = 'Confirmacion de cuenta';

            $mensaje = <<<EOT
            <!DOCTYPE html>
            <html lang="es">

            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Verifica tu cuenta</title>
            </head>

            <body style="font-family: Arial, sans-serif; padding: 20px; text-align: center;">
                <div style="max-width: 400px; margin: auto; background: #ffffff; padding: 20px; border-radius: 8px;">
                    <div style=" color: black; border-radius: 5px;">
                        <h1><strong>MatApp</strong></h1>
                    </div>
                    <h2 style="color: #000;">{$login}, verifica tu cuenta</h2>
                    <p style="color: #333; text-align: left; ">Hola,</p>
                    <p style="color: #555; text-align: left ;">Haz clic en el botón para confirmar tu email.</p>

                    <a href="{$URL_CONFIRMACION}" style="display: inline-block; background-color: #000; color: #ffffff; text-decoration: none; padding: 12px 20px; border-radius: 5px; font-weight: bold; margin-top: 10px;">Confirmar</a>

                    <p style="color: #333; font-size: 14px; margin-top: 20px; text-align: left;">
                        Gracias por confiar en MatApp.
                    </p>

                    <p style="color: #000; font-weight: bold; ">El equipo de MatApp</p>


                </div>
            </body>

            </html>
            EOT;



            $mail->isHTML(true);
            $mail->Body = $mensaje;
            $mail->AltBody = "{$login}, verifica tu cuenta en MatApp. Haz clic en el siguiente enlace para confirmar tu email: {$URL_CONFIRMACION}";

            $mail->send();

            return true;
        } catch (Exception $e) {

            error_log(date('[Y-m-d H:i:s] ') . "Error al enviar el correo: {$mail->ErrorInfo}" . PHP_EOL, 3, __DIR__ . '/../logs/email_errors.log');
            return false;
        }
    }

    /**
     * Muestra el formulario de registro y procesa el envío del mismo.
     * Valida los datos introducidos y registra al usuario si no hay errores.
     */
    public function index()
    {

        if ($_SERVER["REQUEST_METHOD"] == "POST") {


            $errores = [];

            //Valida los datos introducidos en el formulario
            if (isset($_POST["login"])) {
                if (!empty($_POST['login'])) {
                    if (strlen($_POST['login']) < 50) {

                        if ($this->academiaModelo->getUsuarioPorLogin(test_input($_POST["login"]))) {
                            $errores['login_error'] = "El login ya existe";
                            $datos['login'] = "";
                        } else {
                            $login = test_input($_POST["login"]);
                            $datos['login'] = $login;
                        }
                    } else {
                        $errores['login_error'] = "El login es demasiado largo";
                        $datos['login'] = "";
                    }
                } else {
                    $errores['login_error'] = "El login es obligatorio";
                    $datos['login'] = "";
                }
            } else {
                $errores['login_error'] = "El login es obligatorio";
                $datos['login'] = "";
            }

            if (isset($_POST["password"]) && isset($_POST["password2"])) {
                if (!empty($_POST['password'])) {
                    if (!empty($_POST['password2'])) {
                        if (strlen($_POST['password']) < 50 && strlen($_POST['password2']) < 50) {

                            if ($_POST['password'] == $_POST['password2']) {
                                $password = password_hash(test_input($_POST["password"]), PASSWORD_DEFAULT);
                                $datos['password'] = $password;
                            } else {
                                $errores['password_error'] = "Las contraseñas no coinciden";
                                $datos['password'] = "";
                            }
                        } else {
                            $errores['password_error'] = "La contraseña es demasiado larga";
                            $datos['password'] = "";
                        }
                    } else {
                        $errores['password_error'] = "Las contraseñas no coinciden";
                        $datos['password'] = "";
                    }
                } else {
                    $errores['password_error'] = "La contraseña es obligatoria";
                    $datos['password'] = "";
                }
            } else {
                $errores['password_error'] = "La contraseña es obligatoria";
                $datos['password'] = "";
            }


            if (isset($_POST["email"])) {
                if (!empty($_POST['email'])) {
                    if (strlen($_POST['email']) < 50) {

                        if (validar_email($_POST['email'])) {
                            if (!$this->academiaModelo->getUsuarioPorEmail($_POST['email'])) {
                                $email = test_input($_POST["email"]);
                                $datos['email'] = $email;
                            } else {
                                $email_error = "Este email ya ha sido introducido";
                                $errores['email_error'] = $email_error;
                                $datos['email'] = "";
                            }
                        } else {
                            $email_error = "Formato de email inválido";
                            $errores['email_error'] = $email_error;
                            $datos['email'] = "";
                        }
                    } else {
                        $errores['email_error'] = "El email es demasiado largo";
                        $datos['email'] = "";
                    }
                } else {
                    $errores['email_error'] = "El email es obligatorio";
                    $datos['email'] = "";
                }
            } else {
                $errores['email_error'] = "El email es obligatorio";
                $datos['email'] = "";
            }


            // Si no hay errores en el formulario, se procede a registrar al usuario (inactivo)
            if (empty($errores)) {
                $datos['token'] = bin2hex(random_bytes(16));


                if ($this->academiaModelo->registro($datos)) {
                    $datos = [
                        'login' => "",
                        'password' => "",
                        'email' => ""
                    ];
                    if ($this->enviarCorreo($email, $login)) {
                        $datos['success'] = "Se ha enviado un email a " . $email . " para confirmar la cuenta.";
                        header("Location: " . RUTA_URL . "/registroUsuario?errores=" . urlencode(json_encode($datos['errores'])) . "&login=" . urlencode($datos['login']) . "&email=" . urlencode($datos['email']) . "&success=" . urlencode($datos['success']) . "&registro_error=" . urlencode($datos['registro_error']));
                    } else {
                        $datos['no_success'] = "No se ha podido enviar el email de confirmación a " . $email . ", contacte con soporte para activar la cuenta.";
                        header("Location: " . RUTA_URL . "/registroUsuario?errores=" . urlencode(json_encode($datos['errores'])) . "&login=" . urlencode($datos['login']) . "&email=" . urlencode($datos['email']) . "&success=" . urlencode($datos['success']) . "&registro_error=" . urlencode($datos['registro_error']));
                    }
                } else {
                    $datos['registro_error'] = "Error al registrar el usuario, reintentar mas tarde";
                    header("Location: " . RUTA_URL . "/registroUsuario?errores=" . urlencode(json_encode($datos['errores'])) . "&login=" . urlencode($datos['login']) . "&email=" . urlencode($datos['email']) . "&success=" . urlencode($datos['success']) . "&registro_error=" . urlencode($datos['registro_error']));
                }
            } else {
                $datos['errores'] = $errores;
                header("Location: " . RUTA_URL . "/registroUsuario?errores=" . urlencode(json_encode($datos['errores'])) . "&login=" . urlencode($datos['login']) . "&email=" . urlencode($datos['email']) . "&success=" . urlencode($datos['success']) . "&registro_error=" . urlencode($datos['registro_error']));;
            }
        } else {
            $this->blade = new BladeOne($this->views, $this->cache, BladeOne::MODE_DEBUG);
            echo $this->blade->run("registroUsuario", []);
        }
    }

    /**
     * Activa la cuenta del usuario después de hacer clic en el enlace de confirmación enviado por correo.
     */
    public function autenticar()
    {
        if ($_SERVER["REQUEST_METHOD"] == "GET") {

            // Verifica que se reciban los parámetros necesarios por el enlace
            $id = $_GET['id'] ?? null;
            $token = $_GET['token'] ?? null;

            // Si se reciben los parámetros, se procede a activar la cuenta
            if ($id && $token) {
                if ($this->academiaModelo->activarCuenta($id, $token)) {
                    redireccionar('?toastrErr=Cuenta activada correctamente, ya puede iniciar sesión.');
                } else {

                    redireccionar('?toastrErr=Error al activar la cuenta, contacte con soporte para acivarla.');
                }
            } else {
                redireccionar('/');
            }
        } else {
            redireccionar('/');
        }
    }
}
