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

    public function obtenerRolDeUsuario($idUsuario)
    {
        $this->db->query("SELECT r.nombreRol FROM UsuariosRoles ur INNER JOIN Roles r ON ur.idRol = r.idRol WHERE ur.idUsuario = :idUsuario");
        $this->db->bind(':idUsuario', $idUsuario);
        $result = $this->db->registro();
        return $result ? $result->nombreRol : null;
    }

    public function obtenerUsuarioPorLogin($login)
    {
        $this->db->query("SELECT * FROM Usuarios WHERE login = :login");
        $this->db->bind(':login', $login);
        return $this->db->registro();
    }

    public function esAlumno($idAcademia, $idUsuario)
    {
        $this->db->query("SELECT COUNT(*) as count FROM AcademiaUsuarios WHERE idAcademia = :idAcademia AND idUsuario = :idUsuario");
        $this->db->bind(':idAcademia', $idAcademia);
        $this->db->bind(':idUsuario', $idUsuario);
        $result = $this->db->registro();
        return $result && $result->count > 0;
    }

    public function obtenerEstadisticaAcademias()
    {
        // Consulta SQL para obtener el número de alumnos por tipo de academia
        $this->db->query("
        SELECT ta.nombreTipo, COUNT(au.idUsuario) AS numAlumnos
        FROM TipoAcademia ta
        INNER JOIN Academias a ON a.tipoAcademia = ta.idTipo  -- Cambié idTipo por tipoAcademia
        LEFT JOIN AcademiaUsuarios au ON au.idAcademia = a.idAcademia
        GROUP BY ta.idTipo;

    ");

        // Ejecutamos la consulta
        $result = $this->db->registros(); // Usamos 'registros' si queremos obtener todos los resultados

        return $result; // Devolvemos los resultados de la consulta
    }

    public function crearSolicitud($idUsuario, $idAcademia)
    {
        $this->db->query("
            INSERT INTO Solicitudes (idUsuario, idAcademia, fechaSolicitud, estadoSolicitud)
            VALUES (:idUsuario, :idAcademia, :fechaSolicitud, 'pendiente')
        ");
        $this->db->bind(':idUsuario', $idUsuario);
        $this->db->bind(':idAcademia', $idAcademia);
        $this->db->bind(':fechaSolicitud', date('Y-m-d H:i:s'));

        return $this->db->execute(); // Returns true if the query was successful
    }

    public function obtenerSolicitudEnCurso($idUsuario, $idAcademia)
    {
        $this->db->query("
            SELECT * FROM Solicitudes 
            WHERE idUsuario = :idUsuario AND idAcademia = :idAcademia AND estadoSolicitud = 'pendiente'
        ");
        $this->db->bind(':idUsuario', $idUsuario);
        $this->db->bind(':idAcademia', $idAcademia);
        return $this->db->registro(); // Returns the single record if found, null otherwise
    }

    public function obtenerSolicitudesAcademia($idAcademia)
    {
        $this->db->query("
            SELECT s.*, u.nombreUsuario 
            FROM Solicitudes s
            INNER JOIN Usuarios u ON s.idUsuario = u.idUsuario
            WHERE s.idAcademia = :idAcademia AND s.estadoSolicitud = 'pendiente'
        ");
        $this->db->bind(':idAcademia', $idAcademia);
        return $this->db->registros(); // Returns an array of results
    }

    public function aceptarSolicitud($idSolicitud)
    {
        $this->db->query("
            UPDATE Solicitudes 
            SET estadoSolicitud = 'aceptada'
            WHERE idSolicitud = :idSolicitud
        ");
        $this->db->bind(':idSolicitud', $idSolicitud);

        return $this->db->execute(); // Returns true if the query was successful
    }

    public function rechazarSolicitud($idSolicitud)
    {
        $this->db->query("
            UPDATE Solicitudes 
            SET estadoSolicitud = 'rechazada'
            WHERE idSolicitud = :idSolicitud
        ");
        $this->db->bind(':idSolicitud', $idSolicitud);

        return $this->db->execute(); // Returns true if the query was successful
    }

    public function añadirAlumno($idUsuario, $idAcademia)
    {
        $this->db->query("
            INSERT INTO AcademiaUsuarios (idUsuario, idAcademia)
            VALUES (:idUsuario, :idAcademia)
        ");
        $this->db->bind(':idUsuario', $idUsuario);
        $this->db->bind(':idAcademia', $idAcademia);

        return $this->db->execute(); // Returns true if the query was successful
    }
}
