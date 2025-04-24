<?php

use eftec\bladeone\BladeOne;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();
class solicitudesController extends Controlador
{

    private $academiaModelo;
    private $blade;
    private $views = __DIR__ . '/../views';
    private $cache = __DIR__ . '/../cache';

    public function __construct()
    {
        $this->academiaModelo = $this->modelo('academiaModelo');
    }

    public function aceptar()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $idSolicitud = $_POST['id'];
            $idUsuario = $_POST['idUsuario'];
            $idAcademia = $_POST['idAcademia'];

            $resultado = $this->academiaModelo->aceptarSolicitud($idSolicitud);

            header('Content-Type: application/json');
            if ($resultado) {

                $this->academiaModelo->añadirAlumno($idUsuario, $idAcademia);

                echo json_encode(['message' => 'Solicitud aceptada con éxito']);
            } else {
                echo json_encode(['message' => 'Error al aceptar la solicitud']);
            }
        } else {
            redireccionar('/');
        }
    }

    public function rechazar()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $idSolicitud = $_POST['id'];

            $resultado = $this->academiaModelo->rechazarSolicitud($idSolicitud);

            header('Content-Type: application/json');
            if ($resultado) {
                echo json_encode(['message' => 'Solicitud rechazada con éxito']);
            } else {
                echo json_encode(['message' => 'Error al rechazar la solicitud']);
            }
        } else {
            redireccionar('/');
        }
    }

    public function eliminarAlumno()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $idUsuario = $_POST['idUsuario'];
            $idAcademia = $_POST['idAcademia'];

            $resultado = $this->academiaModelo->eliminarAlumno($idUsuario, $idAcademia);

            header('Content-Type: application/json');
            if ($resultado) {
                // Eliminar en caso de que sea entrenador
                $this->academiaModelo->eliminarEntrenador($idUsuario, $idAcademia);

                echo json_encode(['message' => 'Alumno eliminado con éxito']);
            } else {
                echo json_encode(['message' => 'Error al eliminar al alumno']);
            }
        } else {
            redireccionar('/');
        }
    }
}
