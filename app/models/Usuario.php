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

    public function obtenerEventos()
    {
        $this->db->query("SELECT * FROM eventos");
        return $this->db->registros(); // Devuelve todos los registros como objetos
    }

    public function agregarEvento($datos)
    {
        $this->db->query("INSERT INTO eventos (title, start, end) VALUES (:title, :start, :end)");
        $this->db->bind(':title', $datos['title']);
        $this->db->bind(':start', $datos['start']);
        $this->db->bind(':end', $datos['end']);
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function eliminarEvento($id)
    {
        $this->db->query("DELETE FROM eventos WHERE id = :id");
        $this->db->bind(':id', $id);
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function actualizarEvento($id, $datos)
    {
        $this->db->query("UPDATE eventos SET title = :title, start = :start, end = :end WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':title', $datos['title']);
        $this->db->bind(':start', $datos['start']);
        $this->db->bind(':end', $datos['end']);
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
