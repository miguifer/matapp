<?php

// Controlador padre para todos los controladores de la aplicación
// Esquema general
class Controlador
{
    public function modelo($m)
    {
        require_once '../app/models/' . $m . '.php';
        return new $m();
    }
}
