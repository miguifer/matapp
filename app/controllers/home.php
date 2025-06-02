<?php

// Cargar clases de Blade
use eftec\bladeone\BladeOne;

// Controlador de la página de inicio
class home extends Controlador
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

        // Variables pasadas a la vista
        $academias = $this->academiaModelo->obtenerAcademias();
        $mejoresEntrenadores = $this->academiaModelo->obtenerMejoresEntrenadores(5);
        $mejoresAcademias = $this->academiaModelo->obtenerMejoresAcademias(5);
        $datos = [
            'academias' => $academias,
            'mejoresEntrenadores' => $mejoresEntrenadores,
            'mejoresAcademias' => $mejoresAcademias,
        ];

        // Obtener usuarios activos y eliminar sesiones inactivas
        $_SESSION['activos'] = $this->academiaModelo->obtenerUsuariosActivos();
        $this->academiaModelo->eliminarSesionesInactivas();

        // Cargar la vista con Blade
        $this->blade = new BladeOne($this->views, $this->cache, BladeOne::MODE_AUTO);
        echo $this->blade->run("home", $datos);
    }
}
