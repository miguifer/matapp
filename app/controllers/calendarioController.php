<?php


// Controlador para gestionar los calendarios de la aplicación
// Todos endpoints deben ser llamados mediante AJAX desde el javascript  
class calendarioController extends Controlador
{

    private $calendarioModelo;

    public function __construct()
    {
        $this->calendarioModelo = $this->modelo('calendarioModelo');
    }

    /**
     * Método para obtener las clases de una academia específica.
     */
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
                'imagenEntrenador' => isset($clase->imagenEntrenador) && !empty($clase->imagenEntrenador)
                    ? 'data:image/jpeg;base64,' . base64_encode($clase->imagenEntrenador)
                    : null,
            ];
        }
        echo json_encode($clasesArray);
    }

    /**
     * Método para obtener los usuarios reservados en una clase específica.
     */
    public function usuariosReservados()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idClase'])) {
            $idClase = $_POST['idClase'];
            $usuariosReservados = $this->calendarioModelo->obtenerUsuariosReservados($idClase);

            foreach ($usuariosReservados as &$usuario) {
                if (isset($usuario->imagen) && !empty($usuario->imagen)) {
                    $usuario->imagen = 'data:image/jpeg;base64,' . base64_encode($usuario->imagen);
                }
            }
            header('Content-Type: application/json');
            echo json_encode($usuariosReservados);
            return;
        }

        header('Content-Type: application/json', true, 400);
        echo json_encode([
            'error' => 'Solicitud inválida'
        ]);
    }


    /**
     * Método para agregar una nueva clase al calendario.
     */
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

            // Mensajes de respuesta para las alertas
            if ($resultado) {
                echo json_encode(['message' => 'Clase agregada con éxito']);
            } else {
                echo json_encode(['message' => 'Error al guardar en la base de datos']);
            }
        } else {
            redireccionar('/');
        }
    }

    /**
     * Método para actualizar una clase existente en el calendario.
     */
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

    /**
     * Método para eliminar una clase del calendario.
     */
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

    /**
     * Método para reservar una clase por parte de un usuario.
     */
    public function reservar_clase()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $datos = [
                'idClase' => $_POST['idClase'],
                'idUsuario' => $_POST['idUsuario'],
            ];

            // Verificar si el usuario ya tiene una reserva para esta clase
            $reservaExistente = $this->calendarioModelo->verificarReserva($datos['idClase'], $datos['idUsuario']);
            if ($reservaExistente) {
                header('Content-Type: application/json');
                echo json_encode(['message' => 'Ya has reservado esta clase']);
                return;
            }

            // Si no hay reserva existente, proceder a reservar la clase
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

    /**
     * Método para obtener las clases reservadas por un usuario específico.
     * Este método es utilizado por el cliente para mostrar sus reservas en el calendario de su perfil
     */
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

    /**
     * Método para que un usuario se desapunte de una clase.
     */
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


    /**
     * Método para confirmar la asistencia a una clase.
     * Permite confirmar asistencia de múltiples usuarios a una clase específica.
     */
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

    /**
     * Método para valorar una clase por parte de un usuario.
F     */
    public function valorar_clase()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idClase'], $_POST['idUsuario'], $_POST['valoracion'])) {
            $idClase = $_POST['idClase'];
            $idUsuario = $_POST['idUsuario'];
            $valoracion = $_POST['valoracion'];

            $resultado = $this->calendarioModelo->valorarClase($idClase, $idUsuario, $valoracion);

            header('Content-Type: application/json');
            if ($resultado) {
                echo json_encode(['message' => 'Clase valorada con éxito']);
            } else {
                echo json_encode(['message' => 'Error al valorar la clase']);
            }
        } else {
            header('Content-Type: application/json', true, 400);
            echo json_encode(['error' => 'Solicitud inválida: se esperaba POST con idClase, idUsuario y valoracion']);
        }
    }
}
