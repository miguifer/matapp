<?php
session_start();


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

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $login = $_POST['login'];
            $password = $_POST['password'];

            if ($this->academiaModelo->obtenerUsuarioPorLogin($login)) {

                $usuario = $this->academiaModelo->obtenerUsuarioPorLogin($login);

                if (password_verify($password, $usuario->password)) {

                    //usuarios base no estan en roles, asi que si no eisten en la tabla dará null
                    $usuario->rol = $this->academiaModelo->obtenerRolDeUsuario($usuario->idUsuario) ?? 'Cliente';

                    //aqui se guarda el usuario en la sesiiony puedo añadir mas cosas

                    $_SESSION['userLogin'] = [
                        'usuario' => json_encode($usuario),
                    ];

                    redireccionar('/');
                } else {
                    redireccionar('/');
                }
            } else {
                redireccionar('/');
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
        unset($_SESSION['userLogin']);
        redireccionar('/');
    }
}
