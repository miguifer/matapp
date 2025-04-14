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
        return $this->db->registro();
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
}
