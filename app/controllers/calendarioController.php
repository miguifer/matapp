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
        // Comprobar si la solicitud es AJAX
        // Obtener los eventos de la base de datos
        $idAcademia = $_GET['idAcademia'];
        $clases = $this->calendarioModelo->obtenerClases($idAcademia);

        // Devolver respuesta en JSON
        header('Content-Type: application/json');
        // Convertir los eventos a un array asociativo
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

        // Devolver los eventos en formato JSON
        echo json_encode($clasesArray);
    }

    public function usuariosReservados()
    {
        // Solo procesamos POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idClase'])) {
            $idClase = $_POST['idClase'];

            // Obtener los usuarios reservados desde el modelo
            $usuariosReservados = $this->calendarioModelo->obtenerUsuariosReservados($idClase);

            // Devolver la respuesta en formato JSON
            header('Content-Type: application/json');
            echo json_encode($usuariosReservados);
            return;
        }

        // Si no es POST o falta idClase
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

            // Devolver respuesta en JSON
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
                // 'idAcademia' => $_POST['idAcademia'],
            ];

            $resultado = $this->calendarioModelo->actualizarClase($datos['id'], $datos);

            // Devolver respuesta en JSON
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

            // Devolver respuesta en JSON
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

            // Verificar si el usuario ya tiene una reserva para esta clase
            $reservaExistente = $this->calendarioModelo->verificarReserva($datos['idClase'], $datos['idUsuario']);

            // Devolver respuesta en JSON si ya existe la reserva
            if ($reservaExistente) {
                header('Content-Type: application/json');
                echo json_encode(['message' => 'Ya has reservado esta clase']);
                return;
            }

            // Intentar reservar la clase
            $resultado = $this->calendarioModelo->reservarClase($datos);

            // Devolver respuesta en JSON
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

            // Devolver respuesta en JSON
            header('Content-Type: application/json');
            $clasesArray = [];
            foreach ($clases as $clase) {
                $clasesArray[] = [
                    'id' => $clase->id,
                    'title' => $clase->title,
                    'start' => $clase->start,
                    'end' => $clase->end
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

            // Intentar eliminar la reserva de la clase
            $resultado = $this->calendarioModelo->eliminarReserva($datos['idClase'], $datos['idUsuario']);

            // Devolver respuesta en JSON
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
        // Comprobar si la solicitud es AJAX y contiene el idClase
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idClase'])) {
            $idClase = $_POST['idClase'];

            // Obtener los usuarios apuntados desde el modelo
            $usuarios = $this->calendarioModelo->obtenerUsuariosApuntados($idClase);

            // Devolver la respuesta en formato JSON
            header('Content-Type: application/json');
            echo json_encode($usuarios);
        } else {
            // Si no es una solicitud válida, devolver array vacío
            header('Content-Type: application/json');
            echo json_encode([]);
        }
    }
}
