<?php

use eftec\bladeone\BladeOne;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();
class entrenadorController extends Controlador
{

    private $academiaModelo;
    private $blade;
    private $views = __DIR__ . '/../views';
    private $cache = __DIR__ . '/../cache';

    public function __construct()
    {
        $this->academiaModelo = $this->modelo('academiaModelo');
    }

    public function eliminarEntrenador()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $idUsuario = $_POST['idUsuario'];
            $idAcademia = $_POST['idAcademia'];

            $resultado = $this->academiaModelo->eliminarEntrenador($idUsuario, $idAcademia);

            header('Content-Type: application/json');
            if ($resultado) {
                echo json_encode(['message' => 'Alumno eliminado con éxito']);
            } else {
                echo json_encode(['message' => 'Error al eliminar al alumno']);
            }
        } else {
            redireccionar('/');
        }
    }

    public function hacerEntrenador()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $idUsuario = $_POST['idUsuario'];
            $idAcademia = $_POST['idAcademia'];

            $resultado = $this->academiaModelo->hacerEntrenador($idUsuario, $idAcademia);

            header('Content-Type: application/json');
            if ($resultado) {
                echo json_encode(['message' => 'Usuario promovido a entrenador con éxito']);
            } else {
                echo json_encode(['message' => 'Error al promover al usuario a entrenador']);
            }
        } else {
            redireccionar('/');
        }
    }
}
