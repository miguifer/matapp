<?php
session_start();


use eftec\bladeone\BladeOne;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();
class admin extends Controlador
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
        $usuario = json_decode($_SESSION['userLogin']['usuario']);
        $userRole = $usuario->rol;

        if ($usuario && $userRole == 'Administrador') {
            
            $estadisticaAcademia = $this->academiaModelo->obtenerEstadisticaAcademias();
            $datos['estadisticaAcademia'] = $estadisticaAcademia;

            $this->blade = new BladeOne($this->views, $this->cache, BladeOne::MODE_AUTO);
            echo $this->blade->run("admin.inicio", $datos);
        } else {
            redireccionar('/');
        }
    }
}
