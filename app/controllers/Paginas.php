<?php

use eftec\bladeone\BladeOne;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();
class Paginas extends Controlador
{

    private $usuarioModelo;
    private $blade;
    private $views = __DIR__ . '/../views';
    private $cache = __DIR__ . '/../cache';

    public function __construct()
    {
        //1) Acceso al modelo
        $this->usuarioModelo = $this->modelo('Usuario');
    }

    public function index()
    {


        //RESEND EJEMPLO

        // $resend = Resend::client($_ENV['RESEND_API_KEY']);

        // $resend->emails->send([
        //     'from' => 'onboarding@resend.dev',
        //     'to' => 'mfernandezfie@gmail.com',
        //     'subject' => 'Hello World',
        //     'html' => '<p>Has iniciado la página</p>'
        // ]);



        // Podemos pasar parametros a la vista que queramos
        // Para ello nos creamos un array con los parámetros
        $usuarios = $this->usuarioModelo->obtenerUsuarios();
        $datos = [
            'usuarios' => $usuarios, // Array con todos los usuarios
            'name' => 'Miguel',
            'ejemplo' => $_ENV['EJEMPLO'] ?? 'Valor por defecto'
        ];
        // Le pasamos a la vista los parametros
        // Renderiza la vista "welcome" pasando datos

        $this->blade = new BladeOne($this->views, $this->cache, BladeOne::MODE_AUTO);
        echo $this->blade->run("welcome", $datos);
    }

    public function calendario()
    {
        $datos = [
            'todayDate' => new DateTime()
        ];

        $this->blade = new BladeOne($this->views, $this->cache, BladeOne::MODE_AUTO);
        echo $this->blade->run("calendario", $datos);
    }


    public function get_events()
    {
        // Comprobar si la solicitud es AJAX
        // Obtener los eventos de la base de datos
        $eventos = $this->usuarioModelo->obtenerEventos();

        // Devolver respuesta en JSON
        header('Content-Type: application/json');
        // Convertir los eventos a un array asociativo
        $eventosArray = [];
        foreach ($eventos as $evento) {
            $eventosArray[] = [
                'id' => $evento->id,
                'title' => $evento->title,
                'start' => $evento->start,
                'end' => $evento->end //si no funciona forzar null
            ];
        }

        // Devolver los eventos en formato JSON
        echo json_encode($eventosArray);
    }

    public function add_event()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $datos = [
                'title' => $_POST['title'],
                'start' => $_POST['start'],
                'end'   => $_POST['end']
            ];

            $resultado = $this->usuarioModelo->agregarEvento($datos);

            // Devolver respuesta en JSON
            header('Content-Type: application/json');
            if ($resultado) {
                echo json_encode(['message' => 'Evento agregado con éxito']);
            } else {
                echo json_encode(['message' => 'Error al guardar en la base de datos']);
            }
        } else {

            redireccionar('/');
        }
    }

    public function update_event()
    {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $datos = [
                'id' => $_POST['id'],
                'title' => $_POST['title'],
                'start' => $_POST['start'],
                'end'   => $_POST['end']
            ];

            $resultado = $this->usuarioModelo->actualizarEvento($datos['id'], $datos);

            // Devolver respuesta en JSON
            header('Content-Type: application/json');
            if ($resultado) {
                echo json_encode(['message' => 'Evento actualizado con éxito']);
            } else {
                echo json_encode(['message' => 'Error al actualizar en la base de datos']);
            }
        } else {
            redireccionar('/');
        }
    }

    public function delete_event()
    {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];

            $resultado = $this->usuarioModelo->eliminarEvento($id);

            // Devolver respuesta en JSON
            header('Content-Type: application/json');
            if ($resultado) {
                echo json_encode(['message' => 'Evento eliminado con éxito']);
            } else {
                echo json_encode(['message' => 'Error al eliminar en la base de datos']);
            }
        } else {
            redireccionar('/');
        }
    }
}
