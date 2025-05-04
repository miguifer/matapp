<?php

use eftec\bladeone\BladeOne;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();
class calendarioController extends Controlador
{

    private $calendarioModelo;
    private $blade;
    private $views = __DIR__ . '/../views';
    private $cache = __DIR__ . '/../cache';

    public function __construct()
    {
        $this->calendarioModelo = $this->modelo('calendarioModelo');
    }

    public function get_clases()
    {
        $idAcademia = $_GET['idAcademia'];
        $clases = $this->calendarioModelo->obtenerClases($idAcademia);

        header('Content-Type: application/json');
        $clasesArray = [];
        foreach ($clases as $clase) {
            $clasesArray[] = [
                'id' => $clase->id,
                'title' => $clase->title,
                'start' => $clase->start,
                'end' => $clase->end,
                'idEntrenador' => $clase->idEntrenador,
                'nombreEntrenador' => $clase->nombreEntrenador,
            ];
        }
        echo json_encode($clasesArray);
    }

    public function usuariosReservados()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idClase'])) {
            $idClase = $_POST['idClase'];
            $usuariosReservados = $this->calendarioModelo->obtenerUsuariosReservados($idClase);
            header('Content-Type: application/json');
            echo json_encode($usuariosReservados);
            return;
        }
        header('Content-Type: application/json', true, 400);
        echo json_encode([
            'error' => 'Solicitud inválida: se esperaba POST con idClase'
        ]);
    }

    public function add_clase()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $datos = [
                'title' => $_POST['title'],
                'start' => $_POST['start'],
                'end'   => $_POST['end'],
                'idAcademia' => $_POST['idAcademia'],
                'idEntrenador' => $_POST['idEntrenador'],
            ];
            $resultado = $this->calendarioModelo->agregarClase($datos);
            header('Content-Type: application/json');
            if ($resultado) {
                echo json_encode(['message' => 'Clase agregada con éxito']);
            } else {
                echo json_encode(['message' => 'Error al guardar en la base de datos']);
            }
        } else {
            redireccionar('/');
        }
    }

    public function update_clase()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $datos = [
                'id' => $_POST['id'],
                'title' => $_POST['title'],
                'start' => $_POST['start'],
                'end'   => $_POST['end'],
                'idEntrenador' => $_POST['idEntrenador']
            ];
            $resultado = $this->calendarioModelo->actualizarClase($datos['id'], $datos);
            header('Content-Type: application/json');
            if ($resultado) {
                echo json_encode(['message' => 'Clase actualizada con éxito']);
            } else {
                echo json_encode(['message' => 'Error al actualizar en la base de datos']);
            }
        } else {
            redireccionar('/');
        }
    }

    public function delete_clase()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $resultado = $this->calendarioModelo->eliminarClase($id);
            header('Content-Type: application/json');
            if ($resultado) {
                echo json_encode(['message' => 'Clase eliminada con éxito']);
            } else {
                echo json_encode(['message' => 'Error al eliminar en la base de datos']);
            }
        } else {
            redireccionar('/');
        }
    }

    public function reservar_clase()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $datos = [
                'idClase' => $_POST['idClase'],
                'idUsuario' => $_POST['idUsuario'],
            ];
            $reservaExistente = $this->calendarioModelo->verificarReserva($datos['idClase'], $datos['idUsuario']);
            if ($reservaExistente) {
                header('Content-Type: application/json');
                echo json_encode(['message' => 'Ya has reservado esta clase']);
                return;
            }
            $resultado = $this->calendarioModelo->reservarClase($datos);
            header('Content-Type: application/json');
            if ($resultado) {
                echo json_encode(['message' => 'Clase reservada con éxito']);
            } else {
                echo json_encode(['message' => 'Error al reservar la clase']);
            }
        } else {
            redireccionar('/');
        }
    }

    public function get_clases_cliente()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $idUsuario = $_GET['idUsuario'];
            $clases = $this->calendarioModelo->obtenerClasesPorUsuario($idUsuario);
            header('Content-Type: application/json');
            $clasesArray = [];
            foreach ($clases as $clase) {
                $clasesArray[] = [
                    'id' => $clase->id,
                    'title' => $clase->title,
                    'start' => $clase->start,
                    'end' => $clase->end,
                    'nombreEntrenador' => $clase->nombreEntrenador,
                    'apuntados' => $clase->apuntados
                ];
            }
            echo json_encode($clasesArray);
        } else {
            redireccionar('/');
        }
    }

    public function desapuntarse()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $datos = [
                'idClase' => $_POST['idClase'],
                'idUsuario' => $_POST['idUsuario'],
            ];
            $resultado = $this->calendarioModelo->eliminarReserva($datos['idClase'], $datos['idUsuario']);
            header('Content-Type: application/json');
            if ($resultado) {
                echo json_encode(['message' => 'Reserva eliminada con éxito']);
            } else {
                echo json_encode(['message' => 'Error al eliminar la reserva']);
            }
        } else {
            redireccionar('/');
        }
    }

    public function get_usuarios_apuntados()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idClase'])) {
            $idClase = $_POST['idClase'];
            $usuarios = $this->calendarioModelo->obtenerUsuariosApuntados($idClase);
            header('Content-Type: application/json');
            echo json_encode($usuarios);
        } else {
            header('Content-Type: application/json');
            echo json_encode([]);
        }
    }

    public function confirmarAsistencia()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idClase'], $_POST['asistencia'])) {
            $idClase = $_POST['idClase'];
            $asistentes = is_array($_POST['asistencia']) ? $_POST['asistencia'] : [$_POST['asistencia']];
            $resultado = $this->calendarioModelo->confirmarAsistenciaMultiple($idClase, $asistentes);
            header('Content-Type: application/json');
            if ($resultado) {
                echo json_encode(['message' => 'Asistencia confirmada con éxito']);
            } else {
                echo json_encode(['message' => 'Error al confirmar la asistencia']);
            }
        } else {
            redireccionar('/');
        }
    }
}