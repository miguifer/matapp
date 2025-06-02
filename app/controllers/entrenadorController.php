<?php

// Controlador para gestionar entrenadores en la academia
class entrenadorController extends Controlador
{

    private $academiaModelo;

    public function __construct()
    {
        $this->academiaModelo = $this->modelo('academiaModelo');
    }

    // Eliminar un entrenador de la academia
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

    // Hacer a un usuario entrenador
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
