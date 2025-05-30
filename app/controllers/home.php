<?php


use eftec\bladeone\BladeOne;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();
class home extends Controlador
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
        $mejoresEntrenadores = $this->academiaModelo->obtenerMejoresEntrenadores(5);
        $mejoresAcademias = $this->academiaModelo->obtenerMejoresAcademias(5);
        $datos = [
            'academias' => $academias,
            'mejoresEntrenadores' => $mejoresEntrenadores,
            'mejoresAcademias' => $mejoresAcademias,
        ];

        $_SESSION['activos'] = $this->academiaModelo->obtenerUsuariosActivos();

        $this->academiaModelo->eliminarSesionesInactivas();

        $this->blade = new BladeOne($this->views, $this->cache, BladeOne::MODE_AUTO);
        echo $this->blade->run("home", $datos);
    }
}
