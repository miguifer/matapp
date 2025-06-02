<?php

// Controlador de amistades
class amigos extends Controlador
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = $this->modelo('academiaModelo');
    }

    // Buscar usuarios por coincidencia de login
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

        foreach ($usuarios as &$u) {
            if (!empty($u->imagen)) {
                $u->imagen = base64_encode($u->imagen);
            } else {
                $u->imagen = null;
            }
        }

        echo json_encode($usuarios);
    }

    // Solicitar amistad a otro usuario
    public function solicitar()
    {
        header('Content-Type: application/json');

        $usuario = json_decode($_SESSION['userLogin']['usuario']);
        $idUsuario2 = $_POST['idUsuario2'] ?? null;

        if (!$idUsuario2 || $idUsuario2 == $usuario->idUsuario) {
            echo json_encode(['message' => 'Usuario inválido']);
            return;
        }

        // Verificar si la solicitud ya existe o si ya son amigos
        $ok = $this->modelo->enviarSolicitudAmistad($usuario->idUsuario, $idUsuario2);
        echo json_encode(['message' => $ok ? 'Solicitud enviada' : 'Ya existe una solicitud o amistad']);
    }

    // Listar amigos del usuario logueado
    public function lista()
    {
        header('Content-Type: application/json');

        $usuario = json_decode($_SESSION['userLogin']['usuario']);
        $amigos = $this->modelo->obtenerAmigos($usuario->idUsuario);

        foreach ($amigos as &$a) {
            if (!empty($a->imagen)) {
                $a->imagen = base64_encode($a->imagen);
            } else {
                $a->imagen = null;
            }
        }

        echo json_encode($amigos);
    }

    // Listar solicitudes de amistad de usuario logueado
    public function solicitudes()
    {
        header('Content-Type: application/json');

        $usuario = json_decode($_SESSION['userLogin']['usuario']);
        $solicitudes = $this->modelo->obtenerSolicitudesRecibidas($usuario->idUsuario);

        foreach ($solicitudes as &$s) {
            if (!empty($s->imagen)) {
                $s->imagen = base64_encode($s->imagen);
            } else {
                $s->imagen = null;
            }
        }

        echo json_encode($solicitudes);
    }

    // aceptar solicitud de amistad
    public function aceptar()
    {
        header('Content-Type: application/json');

        $usuario = json_decode($_SESSION['userLogin']['usuario']);
        $id = $_POST['id'] ?? null;

        $ok = $this->modelo->aceptarSolicitudAmistad($id, $usuario->idUsuario);

        echo json_encode(['message' => $ok ? 'Solicitud aceptada' : 'Error al aceptar']);
    }

    // rechazar solicitud de amistad
    public function rechazar()
    {
        header('Content-Type: application/json');

        $usuario = json_decode($_SESSION['userLogin']['usuario']);
        $id = $_POST['id'] ?? null;

        $ok = $this->modelo->rechazarSolicitudAmistad($id, $usuario->idUsuario);

        echo json_encode(['message' => $ok ? 'Solicitud rechazada' : 'Error al rechazar']);
    }

    // eliminar amistad
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

    // Obtener perfil de un usuario por ID
    public function perfil()
    {
        $id = $_GET['id'];
        $perfil = $this->modelo->obtenerPerfilPorId($id);
        
        // Para poder pasar la imagen (Blob en BD) a código
        if ($perfil && !empty($perfil->imagen)) {
            $perfil->imagen = base64_encode($perfil->imagen);
        } else if ($perfil) {
            $perfil->imagen = null;
        
        }
        echo json_encode($perfil);
    }
}
