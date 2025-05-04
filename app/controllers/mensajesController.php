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

            $this->academiaModelo->desfijarTodosMensajes($idAcademia);

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

    public function mensajesUsuario()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $usuario = isset($_SESSION['userLogin']['usuario']) ? json_decode($_SESSION['userLogin']['usuario']) : null;
            $idUsuario = $usuario->idUsuario ?? null;
            
            if (!$idUsuario) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'ID de usuario no proporcionado.']);
                exit;
            }

            $mensajes = $this->academiaModelo->obtenerMensajesPorUsuario($idUsuario);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'mensajes' => $mensajes]);
            exit;
        } else {
            redireccionar('/');
        }
    }
}
