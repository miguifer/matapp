<?php

// Cargar clases BladeOne
use eftec\bladeone\BladeOne;

// Controlador para página 'crear una academia'
class crearAcademia extends Controlador
{

    private $blade;
    private $views = __DIR__ . '/../views';
    private $cache = __DIR__ . '/../cache';

    public function index()
    {
        $this->blade = new BladeOne($this->views, $this->cache, BladeOne::MODE_DEBUG);
        echo $this->blade->run("crearAcademia", []);
    }
}
