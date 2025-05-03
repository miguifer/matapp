<?php

use eftec\bladeone\BladeOne;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();
class mensajesController extends Controlador
{

    private $academiaModelo;
    private $blade;
    private $views = __DIR__ . '/../views';
    private $cache = __DIR__ . '/../cache';

    public function __construct()
    {
        $this->academiaModelo = $this->modelo('academiaModelo');
    }

    public function enviarMensaje()
    {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $idUsuario = $_POST['idUsuario'];
            $idACademia = $_POST['idAcademia'];
            $fecha = $_POST['fecha'];
            $mensaje = $_POST['mensaje'];

            $datos = [
                'idUsuario' => $idUsuario,
                'idAcademia' => $idACademia,
                'fecha' => $fecha,
                'mensaje' => $mensaje
            ];
            
            $resultado = $this->academiaModelo->enviarMensaje($mensaje, $datos);

            header('Content-Type: application/json');
            if ($resultado) {
                echo json_encode(['success' => true, 'message' => 'Mensaje enviado correctamente.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al enviar el mensaje.']);
            }
            exit;

        } else {
            redireccionar('/');
        }
    }

    public function fijarMensaje()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $idMensaje = $_POST['idMensaje'];
            $idAcademia = $_POST['idAcademia'];

            // Desfija todos los mensajes de la academia antes de fijar el nuevo
            $this->academiaModelo->desfijarTodosMensajes($idAcademia);

            // Fija el mensaje seleccionado
            $resultado = $this->academiaModelo->fijarMensaje($idMensaje);

            header('Content-Type: application/json');
            if ($resultado) {
                echo json_encode(['success' => true, 'message' => 'Mensaje fijado correctamente.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al fijar el mensaje.']);
            }
            exit;
        } else {
            redireccionar('/');
        }
    }

    public function desfijarMensaje()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $idMensaje = $_POST['idMensaje'];

            $resultado = $this->academiaModelo->desfijarMensaje($idMensaje);

            header('Content-Type: application/json');
            if ($resultado) {
                echo json_encode(['success' => true, 'message' => 'Mensaje desfijado correctamente.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al desfijar el mensaje.']);
            }
            exit;
        } else {
            redireccionar('/');
        }
    }
}
