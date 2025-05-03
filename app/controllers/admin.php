<?php


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

        if ($usuario->rol == 'Administrador') {

            $estadisticaAcademia = $this->academiaModelo->obtenerEstadisticaAcademias();
            $estadisticaUsuarios = $this->academiaModelo->obtenerTotalUsuarios();
            $datos['estadisticaAcademia'] = $estadisticaAcademia;
            $datos['estadisticaUsuarios'] = $estadisticaUsuarios;
            $datos['usuarios'] = $this->academiaModelo->obtenerUsuarios();
            $datos['academias'] = $this->academiaModelo->obtenerAcademias();

            $this->blade = new BladeOne($this->views, $this->cache, BladeOne::MODE_AUTO);
            echo $this->blade->run("admin.inicio", $datos);
        } else {
            redireccionar('/');
        }
    }

    public function historicoClases($academiaId)
    {
        $usuario = json_decode($_SESSION['userLogin']['usuario']);

        if ($usuario->rol == 'Administrador') {
            $resultado = $this->academiaModelo->obtenerHistoricoClases($academiaId);
            header('Content-Type: application/json');
            echo json_encode($resultado);
        } else {
            redireccionar('/');
        }
    }
}
