<?php

class Usuario
{
    private $db;

    public function __construct()
    {
        $this->db = new DataBase();
    }

    public function obtenerUsuarios()
    {
        $this->db->query("SELECT * FROM Usuarios"); // Asegúrate de que Usuarios está en mayúsculas si la tabla se creó así
        return $this->db->registros(); // Devuelve todos los registros como objetos
    }

}
