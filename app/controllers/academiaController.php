<?php
session_start();

use eftec\bladeone\BladeOne;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();
class academiaController extends Controlador
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
        $academia = isset($_GET['academia']) ? json_decode(urldecode($_GET['academia'])) : null;

        $usuario = isset($_SESSION['userLogin']['usuario']) ? json_decode($_SESSION['userLogin']['usuario']) : null;

        $esAlumno = $this->academiaModelo->esAlumno($academia->idAcademia, $usuario->idUsuario);
        $esGerente = $academia->idGerente == $usuario->idUsuario;

        if ($academia == null) {
            redireccionar('/');
        } elseif ($usuario == null || (!$esAlumno && !$esGerente)) {
            redireccionar('/');
        } else {

            $datos = [
                'academia' => $academia,
            ];

            if ($esGerente) {
                $estadisticaAcademia = $this->academiaModelo->obtenerEstadisticaAcademias();
                $datos['estadisticaAcademia'] = $estadisticaAcademia;
            }


            $this->blade = new BladeOne($this->views, $this->cache, BladeOne::MODE_AUTO);
            echo $this->blade->run("academia.inicio", $datos);
        }
    }
}
