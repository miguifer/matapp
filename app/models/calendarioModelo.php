<?php

class calendarioModelo
{
    private $db;

    public function __construct()
    {
        $this->db = new DataBase();
    }
    public function obtenerClases($idAcademia)
    {
        $this->db->query("SELECT c.*, u.nombreUsuario AS nombreEntrenador 
                          FROM clases c
                          LEFT JOIN Usuarios u ON c.idEntrenador = u.idUsuario
                          WHERE c.idAcademia = :idAcademia");
        $this->db->bind(':idAcademia', $idAcademia);
        return $this->db->registros(); // Devuelve todos los registros como objetos
    }

    public function obtenerUsuariosReservados($idClase)
    {
        $this->db->query("SELECT u.* 
                          FROM Reservas r
                          JOIN Usuarios u ON r.idUsuario = u.idUsuario
                          WHERE r.idClase = :idClase");
        $this->db->bind(':idClase', $idClase);
        return $this->db->registros(); // Devuelve todos los registros como objetos
    }

    public function agregarClase($datos)
    {
        $this->db->query("INSERT INTO clases (title, start, end, idAcademia, idEntrenador) VALUES (:title, :start, :end, :idAcademia, :idEntrenador)");
        $this->db->bind(':title', $datos['title']);
        $this->db->bind(':start', $datos['start']);
        $this->db->bind(':end', $datos['end']);
        $this->db->bind(':idAcademia', $datos['idAcademia']);
        $this->db->bind(':idEntrenador', $datos['idEntrenador']);
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function eliminarClase($id)
    {
        $this->db->query("DELETE FROM clases WHERE id = :id");
        $this->db->bind(':id', $id);
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function actualizarClase($id, $datos)
    {
        $this->db->query("UPDATE clases SET title = :title, start = :start, end = :end, idEntrenador = :idEntrenador WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':title', $datos['title']);
        $this->db->bind(':start', $datos['start']);
        $this->db->bind(':end', $datos['end']);
        $this->db->bind(':idEntrenador', $datos['idEntrenador']);
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function reservarClase($datos)
    {
        $this->db->query("INSERT INTO Reservas (idClase, idUsuario) VALUES (:idClase, :idUsuario)");
        $this->db->bind(':idClase', $datos['idClase']);
        $this->db->bind(':idUsuario', $datos['idUsuario']);
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function verificarReserva($idClase, $idUsuario)
    {
        $this->db->query("SELECT * FROM Reservas WHERE idClase = :idClase AND idUsuario = :idUsuario");
        $this->db->bind(':idClase', $idClase);
        $this->db->bind(':idUsuario', $idUsuario);
        return $this->db->registro(); // Devuelve un único registro como objeto o false si no existe
    }

    public function obtenerClasesPorUsuario($idUsuario)
    {
        $this->db->query("SELECT c.* FROM clases c 
                          INNER JOIN Reservas r ON c.id = r.idClase 
                          WHERE r.idUsuario = :idUsuario");
        $this->db->bind(':idUsuario', $idUsuario);
        return $this->db->registros(); // Devuelve todos los registros como objetos
    }

    public function eliminarReserva($idClase, $idUsuario)
    {
        $this->db->query("DELETE FROM Reservas WHERE idClase = :idClase AND idUsuario = :idUsuario");
        $this->db->bind(':idClase', $idClase);
        $this->db->bind(':idUsuario', $idUsuario);
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function obtenerUsuariosApuntados($idClase)
    {
        $this->db->query("SELECT u.nombreUsuario 
                      FROM Reservas r
                      JOIN Usuarios u ON r.idUsuario = u.idUsuario
                      WHERE r.idClase = :idClase");

        $this->db->bind(':idClase', $idClase);

        return $this->db->registros(); // equivalente a fetchAll()
    }
}
