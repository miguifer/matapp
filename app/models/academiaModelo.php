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

    public function obtenerAlumnosAcademia($idAcademia)
    {
        $this->db->query("
            SELECT 
            u.*,
            -- Si el usuario tiene un rol explícito, lo usamos;
            -- si no, comprobamos si es entrenador en esta academia;
            -- en caso contrario, es Cliente
            COALESCE(r.nombreRol,
                CASE 
                    WHEN ae.idAcademiaEntrenador IS NOT NULL THEN 'Entrenador'
                    ELSE 'Cliente'
                END
            ) AS rol
        FROM AcademiaUsuarios au
        INNER JOIN Usuarios u 
            ON au.idUsuario = u.idUsuario
        LEFT JOIN UsuariosRoles ur 
            ON ur.idUsuario = u.idUsuario
        LEFT JOIN Roles r 
            ON r.idRol = ur.idRol
        -- Unimos también con la tabla de entrenadores, 
        -- pero solo en esta misma academia
        LEFT JOIN AcademiasEntrenadores ae 
            ON ae.idUsuario    = u.idUsuario
           AND ae.idACademia  = au.idAcademia
        WHERE au.idAcademia = :idAcademia
        ");
        $this->db->bind(':idAcademia', $idAcademia);
        return $this->db->registros(); // Returns an array of results
    }

    public function eliminarAlumno($idUsuario, $idAcademia)
    {
        $this->db->query("
            DELETE FROM AcademiaUsuarios 
            WHERE idUsuario = :idUsuario AND idAcademia = :idAcademia
        ");
        $this->db->bind(':idUsuario', $idUsuario);
        $this->db->bind(':idAcademia', $idAcademia);

        return $this->db->execute(); // Returns true if the query was successful
    }

    public function obtenerEntrenadoresAcademia($idAcademia)
    {
        $this->db->query("SELECT u.*
            FROM Usuarios u
            INNER JOIN AcademiasEntrenadores ae ON u.idUsuario = ae.idUsuario
            WHERE ae.idAcademia = :idAcademia");
        $this->db->bind(':idAcademia', $idAcademia);
        return $this->db->registros();  // Returns true if the query was successful

    }

    public function eliminarEntrenador($idUsuario, $idAcademia)
    {
        $this->db->query("
            DELETE FROM AcademiasEntrenadores 
            WHERE idUsuario = :idUsuario AND idAcademia = :idAcademia
        ");
        $this->db->bind(':idUsuario', $idUsuario);
        $this->db->bind(':idAcademia', $idAcademia);

        return $this->db->execute(); // Returns true if the query was successful
    }

    public function hacerEntrenador($idUsuario, $idAcademia)
    {
        $this->db->query("
            INSERT INTO AcademiasEntrenadores (idUsuario, idAcademia)
            VALUES (:idUsuario, :idAcademia)
        ");
        $this->db->bind(':idUsuario', $idUsuario);
        $this->db->bind(':idAcademia', $idAcademia);

        return $this->db->execute(); // Returns true if the query was successful
    }

    public function esEntrenador($idAcademia, $idUsuario)
    {
        $this->db->query("
        SELECT COUNT(*) as count 
        FROM AcademiasEntrenadores 
        WHERE idAcademia = :idAcademia AND idUsuario = :idUsuario
    ");
        $this->db->bind(':idAcademia', $idAcademia);
        $this->db->bind(':idUsuario', $idUsuario);
        $result = $this->db->registro();
        return $result && $result->count > 0;
    }

    public function obtenerTotalUsuarios()
    {
        $this->db->query("
            SELECT COUNT(*) AS totalUsuarios
            FROM Usuarios
        ");

        $result = $this->db->registro(); // Assuming registro() fetches a single record
        return $result ? $result->totalUsuarios : 0; // Returns the total count of users
    }

    //SESIONES ACTIVAS
    public function actualizarActividad($userId)
    {
        $this->db->query("
        REPLACE INTO sesiones_activas (idUsuario, last_activity)
        VALUES (:user_id, datetime('now'))
    ");
        $this->db->bind(':user_id', $userId);
        $this->db->execute();
    }

    public function obtenerUsuariosActivos($minutos = 5)
    {
        $this->db->query("
        SELECT COUNT(*) AS totalActivos
        FROM sesiones_activas
        WHERE last_activity >= datetime('now', '-' || :minutos || ' minutes')
    ");
        $this->db->bind(':minutos', $minutos);
        $resultado = $this->db->registro();
        return $resultado ? $resultado->totalActivos : 0;
    }

    public function eliminarSesionesInactivas()
    {
        $this->db->query("
        DELETE FROM sesiones_activas
        WHERE last_activity < datetime('now', '-30 minutes')
    ");
        $this->db->execute();
    }

    public function getUsuarioPorLogin($login)
    {
        $this->db->query("SELECT * FROM Usuarios WHERE login = :login");
        $this->db->bind(':login', $login);
        return $this->db->registro(); // Assuming this returns a single record
    }

    public function getUsuarioPorEmail($emailUsuario)
    {
        $this->db->query("SELECT * FROM Usuarios WHERE emailUsuario = :email");
        $this->db->bind(':email', $emailUsuario);
        return $this->db->registro(); // Assuming this returns a single record
    }

    public function registro($datos)
    {
        $this->db->query("INSERT INTO usuarios (login, password, emailUsuario, activo, token) VALUES (:login, :password, :email, 0, :token)");
        $this->db->bind(":login", $datos['login']);
        $this->db->bind(":password", $datos['password']);
        $this->db->bind(":email", $datos['email']);
        $this->db->bind(":token", $datos['token']);


        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function activarCuenta($id, $token)
    {
        $this->db->query("UPDATE usuarios SET activo = 1 WHERE idUsuario = :id AND token = :token");
        $this->db->bind(":id", $id);
        $this->db->bind(":token", $token);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function eliminarCuenta($idUsuario)
    {
        $this->db->query("DELETE FROM Usuarios WHERE idUsuario = :idUsuario");
        $this->db->bind(':idUsuario', $idUsuario);

        return $this->db->execute(); // Returns true if the query was successful
    }

    public function getUsuarioPorId($idUsuario)
    {
        $this->db->query("SELECT * FROM Usuarios WHERE idUsuario = :idUsuario");
        $this->db->bind(':idUsuario', $idUsuario);
        return $this->db->registro(); // Returns the user record or null if not found
    }

    public function modificarUsuario($id, $datos)
    {
        $this->db->query("UPDATE usuarios SET email = :email, login = :login WHERE id = :id");
        $this->db->bind(":id", $id);
        $this->db->bind(":email", $datos['email']);
        $this->db->bind(":login", $datos['login']);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function modificarImagen($id, $datos)
    {
        $this->db->query("UPDATE usuarios SET imagen = :imagen WHERE idUsuario = :id");
        $this->db->bind(":id", $id);
        $this->db->bind(":imagen", $datos['imagen'], PDO::PARAM_LOB);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }  
}
