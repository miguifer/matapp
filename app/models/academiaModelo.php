<?php

class academiaModelo
{
    private $db;

    public function __construct()
    {
        $this->db = new DataBase();
    }

    public function obtenerAcademias()
    {
        $this->db->query("SELECT * FROM Academias"); 
        return $this->db->registros(); // Assuming registros() returns an array of results
    }
    
}
