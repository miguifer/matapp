<?php


// Cargar clases para vistas Blade
use eftec\bladeone\BladeOne;

// Controlador para el panel de administración
class admin extends Controlador
{

    private $academiaModelo;
    private $blade;
    private $views = __DIR__ . '/../views';
    private $cache = __DIR__ . '/../cache';

    public function __construct()
    {
        $this->academiaModelo = $this->modelo('academiaModelo');
    }

    public function index()
    {
        $usuario = json_decode($_SESSION['userLogin']['usuario']);

        // Verificar si el usuario es un administrador
        if ($usuario->rol == 'Administrador') {

            //Datos para la vista del panel
            $estadisticaAcademia = $this->academiaModelo->obtenerEstadisticaAcademias();
            $estadisticaUsuarios = $this->academiaModelo->obtenerTotalUsuarios();
            $datos['estadisticaAcademia'] = $estadisticaAcademia;
            $datos['estadisticaUsuarios'] = $estadisticaUsuarios;
            $datos['usuarios'] = $this->academiaModelo->obtenerUsuarios();
            $datos['academias'] = $this->academiaModelo->obtenerAcademias();
            $datos['estadisticaAcademiaModalidad'] = $this->academiaModelo->obtenerEstadisticaModalidadAcademias();

            $this->blade = new BladeOne($this->views, $this->cache, BladeOne::MODE_AUTO);
            echo $this->blade->run("admin.inicio", $datos);
        } else {
            redireccionar('/');
        }
    }

    // Obtener histórico de clases de una academia
    public function historicoClases($academiaId)
    {
        $usuario = json_decode($_SESSION['userLogin']['usuario']);

        if ($usuario->rol == 'Administrador') {
            $resultado = $this->academiaModelo->obtenerHistoricoClases($academiaId);
            header('Content-Type: application/json');
            echo json_encode($resultado);
        } else {
            redireccionar('/');
        }
    }

    // Obtener participantes de una clase específica
    public function participantesClase($claseId)
    {
        $usuario = json_decode($_SESSION['userLogin']['usuario']);

        if ($usuario->rol == 'Administrador') {
            $participantes = $this->academiaModelo->obtenerParticipantesClase($claseId);
            header('Content-Type: application/json');
            echo json_encode($participantes);
        } else {
            redireccionar('/');
        }
    }
}
