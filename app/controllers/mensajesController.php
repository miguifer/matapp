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
}
