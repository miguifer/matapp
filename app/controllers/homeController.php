<?php
session_start();


use eftec\bladeone\BladeOne;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();
class homeController extends Controlador
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

        $academias = $this->academiaModelo->obtenerAcademias();
        $datos = [
            'academias' => $academias,
        ];

        $this->blade = new BladeOne($this->views, $this->cache, BladeOne::MODE_AUTO);
        echo $this->blade->run("home", $datos);
    }

    public function inicioSesion()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $login = $_POST['login'];
            $password = $_POST['password'];

            if ($this->academiaModelo->obtenerUsuarioPorLogin($login)) {

                $usuario = $this->academiaModelo->obtenerUsuarioPorLogin($login);

                if (password_verify($password, $usuario->password)) {

                    //usuarios base no estan en roles, asi que si no eisten en la tabla dará null
                    $usuario->rol = $this->academiaModelo->obtenerRolDeUsuario($usuario->idUsuario) ?? 'cliente';
                    
                    //aqui se guarda el usuario en la sesiiony puedo añadir mas cosas

                    $_SESSION['userLogin'] = [
                        'usuario' => json_encode($usuario),
                    ];

                    redireccionar('/');
                }
            }
        } else if (isset($_SESSION['userLogin'])) {
            redireccionar('/');
        } else {
            $this->blade = new BladeOne($this->views, $this->cache, BladeOne::MODE_AUTO);
            echo $this->blade->run("inicioSesion", []);
        }
    }
}
