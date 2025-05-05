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
        $this->db->query("SELECT c.*, u.nombreUsuario AS nombreEntrenador, u.imagen AS imagenEntrenador
                          FROM clases c
                          LEFT JOIN Usuarios u ON c.idEntrenador = u.idUsuario
                          WHERE c.idAcademia = :idAcademia");
        $this->db->bind(':idAcademia', $idAcademia);
        return $this->db->registros();
    }

    public function obtenerUsuariosReservados($idClase)
    {
        $this->db->query("SELECT u.idUsuario, u.nombreUsuario, u.login, u.imagen, u.emailUsuario , r.asistencia
                      FROM Reservas r
                      JOIN Usuarios u ON r.idUsuario = u.idUsuario
                      WHERE r.idClase = :idClase");
        $this->db->bind(':idClase', $idClase);
        return $this->db->registros();
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
        return $this->db->registro();
    }

    public function obtenerClasesPorUsuario($idUsuario)
    {
        $this->db->query("SELECT c.*, u.nombreUsuario AS nombreEntrenador
                          FROM clases c
                          LEFT JOIN Usuarios u ON c.idEntrenador = u.idUsuario
                          INNER JOIN Reservas r ON c.id = r.idClase
                          WHERE r.idUsuario = :idUsuario");
        $this->db->bind(':idUsuario', $idUsuario);
        $clases = $this->db->registros();

        foreach ($clases as &$clase) {
            $this->db->query("SELECT u.nombreUsuario FROM Reservas r JOIN Usuarios u ON r.idUsuario = u.idUsuario WHERE r.idClase = :idClase");
            $this->db->bind(':idClase', $clase->id);
            $usuarios = $this->db->registros();
            $clase->apuntados = array_map(function ($u) {
                return $u->nombreUsuario;
            }, $usuarios);
        }
        return $clases;
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

        return $this->db->registros();
    }

    public function confirmarAsistenciaMultiple($idClase, $asistentes)
    {
        try {
            $this->db->query("UPDATE Reservas SET asistencia = 0 WHERE idClase = :idClase");
            $this->db->bind(':idClase', $idClase);
            $this->db->execute();

            if (!empty($asistentes)) {
                $placeholders = implode(',', array_fill(0, count($asistentes), '?'));
                $sql = "UPDATE Reservas SET asistencia = 1 WHERE idClase = ? AND idUsuario IN ($placeholders)";
                $this->db->query($sql);

                $params = array_merge([$idClase], $asistentes);
                foreach ($params as $idx => $val) {
                    $this->db->bind($idx + 1, $val);
                }
                $this->db->execute();
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
