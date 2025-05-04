<?php

class amigos extends Controlador
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = $this->modelo('academiaModelo');
    }

    public function buscar()
    {
        header('Content-Type: application/json');
        $usuario = json_decode($_SESSION['userLogin']['usuario']);
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        if (strlen($q) < 2) {
            echo json_encode([]);
            return;
        }
        $usuarios = $this->modelo->buscarUsuariosParaAmistad($usuario->idUsuario, $q);
        echo json_encode($usuarios);
    }

    public function solicitar()
    {
        header('Content-Type: application/json');
        $usuario = json_decode($_SESSION['userLogin']['usuario']);
        $idUsuario2 = $_POST['idUsuario2'] ?? null;
        if (!$idUsuario2 || $idUsuario2 == $usuario->idUsuario) {
            echo json_encode(['message' => 'Usuario inválido']);
            return;
        }
        $ok = $this->modelo->enviarSolicitudAmistad($usuario->idUsuario, $idUsuario2);
        echo json_encode(['message' => $ok ? 'Solicitud enviada' : 'Ya existe una solicitud o amistad']);
    }

    public function lista()
    {
        header('Content-Type: application/json');
        $usuario = json_decode($_SESSION['userLogin']['usuario']);
        $amigos = $this->modelo->obtenerAmigos($usuario->idUsuario);
        echo json_encode($amigos);
    }

    public function solicitudes()
    {
        header('Content-Type: application/json');
        $usuario = json_decode($_SESSION['userLogin']['usuario']);
        $solicitudes = $this->modelo->obtenerSolicitudesRecibidas($usuario->idUsuario);
        echo json_encode($solicitudes);
    }

    public function aceptar()
    {
        header('Content-Type: application/json');
        $usuario = json_decode($_SESSION['userLogin']['usuario']);
        $id = $_POST['id'] ?? null;
        $ok = $this->modelo->aceptarSolicitudAmistad($id, $usuario->idUsuario);
        echo json_encode(['message' => $ok ? 'Solicitud aceptada' : 'Error al aceptar']);
    }

    public function rechazar()
    {
        header('Content-Type: application/json');
        $usuario = json_decode($_SESSION['userLogin']['usuario']);
        $id = $_POST['id'] ?? null;
        $ok = $this->modelo->rechazarSolicitudAmistad($id, $usuario->idUsuario);
        echo json_encode(['message' => $ok ? 'Solicitud rechazada' : 'Error al rechazar']);
    }

    public function eliminar()
    {
        header('Content-Type: application/json');
        $usuario = json_decode($_SESSION['userLogin']['usuario']);
        $amigoId = $_POST['id'] ?? null;
        if (!$amigoId) {
            echo json_encode(['message' => 'Usuario inválido']);
            return;
        }
        $ok = $this->modelo->eliminarAmistad($usuario->idUsuario, $amigoId);
        echo json_encode(['message' => $ok ? 'Amistad eliminada' : 'Error al eliminar']);
    }
}