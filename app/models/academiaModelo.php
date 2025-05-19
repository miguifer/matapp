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
        $this->db->query("
        SELECT a.*, ta.nombreTipo AS tipoAcademia
        FROM Academias a
        INNER JOIN TipoAcademia ta ON a.tipoAcademia = ta.idTipo
    ");
        return $this->db->registros();
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
        $this->db->query("
        SELECT ta.nombreTipo, COUNT(au.idUsuario) AS numAlumnos
        FROM TipoAcademia ta
        INNER JOIN Academias a ON a.tipoAcademia = ta.idTipo
        LEFT JOIN AcademiaUsuarios au ON au.idAcademia = a.idAcademia
        GROUP BY ta.idTipo;
    ");
        $result = $this->db->registros();
        return $result;
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
        return $this->db->execute();
    }

    public function obtenerSolicitudEnCurso($idUsuario, $idAcademia)
    {
        $this->db->query("
            SELECT * FROM Solicitudes 
            WHERE idUsuario = :idUsuario AND idAcademia = :idAcademia AND estadoSolicitud = 'pendiente'
        ");
        $this->db->bind(':idUsuario', $idUsuario);
        $this->db->bind(':idAcademia', $idAcademia);
        return $this->db->registro();
    }

    public function obtenerSolicitudesAcademia($idAcademia)
    {
        $this->db->query("
            SELECT s.*, u.nombreUsuario, u.login, u.emailUsuario, u.imagen
            FROM Solicitudes s
            INNER JOIN Usuarios u ON s.idUsuario = u.idUsuario
            WHERE s.idAcademia = :idAcademia AND s.estadoSolicitud = 'pendiente'
        ");
        $this->db->bind(':idAcademia', $idAcademia);
        return $this->db->registros();
    }

    public function aceptarSolicitud($idSolicitud)
    {
        $this->db->query("
            UPDATE Solicitudes 
            SET estadoSolicitud = 'aceptada'
            WHERE idSolicitud = :idSolicitud
        ");
        $this->db->bind(':idSolicitud', $idSolicitud);
        return $this->db->execute();
    }

    public function rechazarSolicitud($idSolicitud)
    {
        $this->db->query("
            UPDATE Solicitudes 
            SET estadoSolicitud = 'rechazada'
            WHERE idSolicitud = :idSolicitud
        ");
        $this->db->bind(':idSolicitud', $idSolicitud);
        return $this->db->execute();
    }

    public function añadirAlumno($idUsuario, $idAcademia)
    {
        $this->db->query("
            INSERT INTO AcademiaUsuarios (idUsuario, idAcademia)
            VALUES (:idUsuario, :idAcademia)
        ");
        $this->db->bind(':idUsuario', $idUsuario);
        $this->db->bind(':idAcademia', $idAcademia);
        return $this->db->execute();
    }

    public function obtenerAlumnosAcademia($idAcademia)
    {
        $this->db->query("
            SELECT 
            u.*,
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
        LEFT JOIN AcademiasEntrenadores ae 
            ON ae.idUsuario    = u.idUsuario
           AND ae.idACademia  = au.idAcademia
        WHERE au.idAcademia = :idAcademia
        ");
        $this->db->bind(':idAcademia', $idAcademia);
        return $this->db->registros();
    }

    public function eliminarAlumno($idUsuario, $idAcademia)
    {
        $this->db->query("
            DELETE FROM AcademiaUsuarios 
            WHERE idUsuario = :idUsuario AND idAcademia = :idAcademia
        ");
        $this->db->bind(':idUsuario', $idUsuario);
        $this->db->bind(':idAcademia', $idAcademia);
        return $this->db->execute();
    }

    public function obtenerEntrenadoresAcademia($idAcademia)
    {
        $this->db->query("SELECT u.*
            FROM Usuarios u
            INNER JOIN AcademiasEntrenadores ae ON u.idUsuario = ae.idUsuario
            WHERE ae.idAcademia = :idAcademia");
        $this->db->bind(':idAcademia', $idAcademia);
        return $this->db->registros();
    }

    public function eliminarEntrenador($idUsuario, $idAcademia)
    {
        $this->db->query("
            DELETE FROM AcademiasEntrenadores 
            WHERE idUsuario = :idUsuario AND idAcademia = :idAcademia
        ");
        $this->db->bind(':idUsuario', $idUsuario);
        $this->db->bind(':idAcademia', $idAcademia);
        return $this->db->execute();
    }

    public function hacerEntrenador($idUsuario, $idAcademia)
    {
        $this->db->query("
            INSERT INTO AcademiasEntrenadores (idUsuario, idAcademia)
            VALUES (:idUsuario, :idAcademia)
        ");
        $this->db->bind(':idUsuario', $idUsuario);
        $this->db->bind(':idAcademia', $idAcademia);
        return $this->db->execute();
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
        $result = $this->db->registro();
        return $result ? $result->totalUsuarios : 0;
    }

    public function actualizarActividad($userId)
    {
        $this->db->query("
        REPLACE INTO sesiones_activas (idUsuario, last_activity)
        VALUES (:user_id, datetime('now'))
    ");
        $this->db->bind(':user_id', $userId);
        $this->db->execute();
    }

    public function obtenerUsuariosActivos($minutos = 30)
    {
        $this->db->query("
            SELECT idUsuario
            FROM sesiones_activas
            WHERE last_activity >= datetime('now', '-' || :minutos || ' minutes')
        ");
        $this->db->bind(':minutos', $minutos);
        return $this->db->registros();
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
        return $this->db->registro();
    }

    public function getUsuarioPorEmail($emailUsuario)
    {
        $this->db->query("SELECT * FROM Usuarios WHERE emailUsuario = :email");
        $this->db->bind(':email', $emailUsuario);
        return $this->db->registro();
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
        return $this->db->execute();
    }

    public function getUsuarioPorId($idUsuario)
    {
        $this->db->query("SELECT * FROM Usuarios WHERE idUsuario = :idUsuario");
        $this->db->bind(':idUsuario', $idUsuario);
        return $this->db->registro();
    }

    public function modificarUsuario($id, $datos)
    {
        $query = "UPDATE usuarios SET 
            emailUsuario = :email, 
            login = :login, 
            nombreUsuario = :nombreUsuario, 
            apellido1Usuario = :apellido1Usuario, 
            apellido2Usuario = :apellido2Usuario, 
            telefonoUsuario = :telefonoUsuario";
        if (isset($datos['password'])) {
            $query .= ", password = :password";
        }
        $query .= " WHERE idUsuario = :id";
        $this->db->query($query);
        $this->db->bind(":id", $id);
        $this->db->bind(":email", $datos['email']);
        $this->db->bind(":login", $datos['login']);
        $this->db->bind(":nombreUsuario", $datos['nombreUsuario']);
        $this->db->bind(":apellido1Usuario", $datos['apellido1Usuario']);
        $this->db->bind(":apellido2Usuario", $datos['apellido2Usuario']);
        $this->db->bind(":telefonoUsuario", $datos['telefonoUsuario']);
        if (isset($datos['password'])) {
            $this->db->bind(":password", $datos['password']);
        }
        return $this->db->execute();
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

    public function getSolicitudesPorIdUsuario($idUsuario)
    {
        $this->db->query("
            SELECT s.*, a.nombreAcademia, a.path_imagen
            FROM Solicitudes s
            INNER JOIN Academias a ON s.idAcademia = a.idAcademia
            WHERE s.idUsuario = :idUsuario
            ORDER BY s.fechaSolicitud DESC
        ");
        $this->db->bind(':idUsuario', $idUsuario);
        return $this->db->registros();
    }

    public function enviarMensaje($mensaje, $datos)
    {
        $this->db->query("
            INSERT INTO muro_mensajes (idUsuario, idAcademia, fecha, mensaje)
            VALUES (:idUsuario, :idAcademia, :fecha, :mensaje)
        ");
        $this->db->bind(':idUsuario', $datos['idUsuario']);
        $this->db->bind(':idAcademia', $datos['idAcademia']);
        $this->db->bind(':fecha', $datos['fecha']);
        $this->db->bind(':mensaje', $mensaje);
        return $this->db->execute();
    }

    public function obtenerMensajesAcademia($idAcademia)
    {
        $this->db->query("
            SELECT mm.*, u.nombreUsuario, u.login, u.imagen, r.nombreRol
            FROM muro_mensajes mm
            INNER JOIN Usuarios u ON mm.idUsuario = u.idUsuario
            LEFT JOIN UsuariosRoles ur ON u.idUsuario = ur.idUsuario
            LEFT JOIN Roles r ON ur.idRol = r.idRol
            WHERE mm.idAcademia = :idAcademia
            ORDER BY mm.fecha DESC
        ");
        $this->db->bind(':idAcademia', $idAcademia);
        return $this->db->registros();
    }

    public function obtenerUsuarios()
    {
        $this->db->query("SELECT * FROM Usuarios");
        return $this->db->registros();
    }

    public function obtenerClasesAcademia($idAcademia)
    {
        $this->db->query("
            SELECT c.*, u.nombreUsuario AS nombreEntrenador, u.login AS loginEntrenador, u.imagen
            FROM Clases c
            LEFT JOIN Usuarios u ON c.idEntrenador = u.idUsuario
            WHERE c.idAcademia = :idAcademia
            ORDER BY c.start DESC
        ");
        $this->db->bind(':idAcademia', $idAcademia);
        return $this->db->registros();
    }

    public function getAsistenciaPorIdUsuario($idUsuario)
    {
        $this->db->query("
        SELECT 
            r.*, 
            c.title AS nombreClase, 
            c.start AS fecha, 
            a.nombreAcademia, 
            u.nombreUsuario AS nombreEntrenador
        FROM Reservas r
        INNER JOIN Clases c ON r.idClase = c.id
        INNER JOIN Academias a ON c.idAcademia = a.idAcademia
        LEFT JOIN Usuarios u ON c.idEntrenador = u.idUsuario
        WHERE r.idUsuario = :idUsuario
        AND (r.asistencia = 1 OR r.asistencia IS NULL)
        ORDER BY c.start DESC
    ");
        $this->db->bind(':idUsuario', $idUsuario);
        return $this->db->registros();
    }

    public function getRankingAsistencia($idAcademia)
    {
        $this->db->query("
        SELECT u.nombreUsuario, u.login, u.imagen, COUNT(r.asistencia) AS total_asistencias
        FROM Reservas r
        INNER JOIN Usuarios u ON r.idUsuario = u.idUsuario
        INNER JOIN Clases c ON r.idClase = c.id
        WHERE c.idAcademia = :idAcademia AND r.asistencia = 1
        GROUP BY u.idUsuario
        ORDER BY total_asistencias DESC
    ");
        $this->db->bind(':idAcademia', $idAcademia);
        return $this->db->registros();
    }

    public function obtenerHistoricoClases($idAcademia)
    {
        $this->db->query("
            SELECT 
            c.*, 
            a.nombreAcademia, 
            u.nombreUsuario AS entrenador
            FROM Clases c
            INNER JOIN Academias a ON c.idAcademia = a.idAcademia
            LEFT JOIN Usuarios u ON c.idEntrenador = u.idUsuario
            WHERE c.idAcademia = :idAcademia
            ORDER BY c.start ASC
        ");
        $this->db->bind(':idAcademia', $idAcademia);
        return $this->db->registros();
    }
    public function obtenerEstadisticaModalidadAcademias()
    {
        $this->db->query("
            SELECT ta.nombreTipo AS modalidad, COUNT(a.idAcademia) AS numAcademias
            FROM TipoAcademia ta
            LEFT JOIN Academias a ON a.tipoAcademia = ta.idTipo
            GROUP BY ta.idTipo
        ");
        return $this->db->registros();
    }

    public function fijarMensaje($idMensaje)
    {
        $this->db->query("UPDATE muro_mensajes SET fijado = 1 WHERE idMensaje = :idMensaje");
        $this->db->bind(':idMensaje', $idMensaje);
        return $this->db->execute();
    }

    public function desfijarMensaje($idMensaje)
    {
        $this->db->query("UPDATE muro_mensajes SET fijado = 0 WHERE idMensaje = :idMensaje");
        $this->db->bind(':idMensaje', $idMensaje);
        return $this->db->execute();
    }

    public function desfijarTodosMensajes($idAcademia)
    {
        $this->db->query("UPDATE muro_mensajes SET fijado = 0 WHERE idAcademia = :idAcademia");
        $this->db->bind(':idAcademia', $idAcademia);
        return $this->db->execute();
    }

    public function obtenerParticipantesClase($claseId)
    {
        $this->db->query("
        SELECT 
            u.idUsuario,
            u.login,
            u.emailUsuario,
            u.activo,
            u.token,
            u.nombreUsuario,
            u.apellido1Usuario,
            u.apellido2Usuario,
            u.telefonoUsuario,
            r.asistencia
        FROM Reservas r
        INNER JOIN Usuarios u ON r.idUsuario = u.idUsuario
        WHERE r.idClase = :claseId
    ");
        $this->db->bind(':claseId', $claseId);
        return $this->db->registros();
    }

    public function obtenerMensajesPorUsuario($idUsuario)
    {
        $this->db->query("
        SELECT 
            mm.mensaje,
            mm.fecha,
            a.nombreAcademia AS academia,
            mm.idAcademia,
            mm.idMensaje
        FROM muro_mensajes mm
        INNER JOIN Academias a ON mm.idAcademia = a.idAcademia
        INNER JOIN AcademiaUsuarios au ON mm.idAcademia = au.idAcademia
        WHERE au.idUsuario = :idUsuario
        AND mm.fecha >= datetime('now', '-7 days')
        ORDER BY mm.fecha DESC
        LIMIT 10;

    ");
        $this->db->bind(':idUsuario', $idUsuario);
        return $this->db->registros();
    }
    public function actualizarInfoAcademia($idAcademia, $nombre, $ubicacion)
    {
        $this->db->query("
            UPDATE Academias
            SET nombreAcademia = :nombre, ubicacionAcademia = :ubicacion
            WHERE idAcademia = :idAcademia
        ");
        $this->db->bind(':nombre', $nombre);
        $this->db->bind(':ubicacion', $ubicacion);
        $this->db->bind(':idAcademia', $idAcademia);
        return $this->db->execute();
    }

    public function buscarUsuariosParaAmistad($miId, $q)
    {
        $this->db->query("
        SELECT 
            u.idUsuario, 
            u.login,
            u.imagen,
            a.estado
        FROM Usuarios u
        LEFT JOIN Amistades a 
            ON (
                (a.idUsuario1 = :miId AND a.idUsuario2 = u.idUsuario)
                OR (a.idUsuario2 = :miId AND a.idUsuario1 = u.idUsuario)
            )
        WHERE u.idUsuario != :miId
        AND u.idUsuario != 1
        AND u.login LIKE :q
        LIMIT 10
    ");
        $this->db->bind(':miId', $miId);
        $this->db->bind(':q', "%$q%");
        return $this->db->registros();
    }

    public function enviarSolicitudAmistad($yo, $otro)
    {
        $this->db->query("SELECT * FROM Amistades WHERE 
        (idUsuario1 = :yo AND idUsuario2 = :otro) OR 
        (idUsuario1 = :otro AND idUsuario2 = :yo)");
        $this->db->bind(':yo', $yo);
        $this->db->bind(':otro', $otro);
        if ($this->db->registro()) return false;
        $this->db->query("INSERT INTO Amistades (idUsuario1, idUsuario2, estado) VALUES (:yo, :otro, 'pendiente')");
        $this->db->bind(':yo', $yo);
        $this->db->bind(':otro', $otro);
        return $this->db->execute();
    }

    public function obtenerAmigos($miId)
    {
        $this->db->query("
        SELECT 
            u.idUsuario, 
            u.login, 
            u.imagen,
            CASE WHEN sa.idUsuario IS NOT NULL THEN 1 ELSE 0 END AS online
        FROM Amistades a
        JOIN Usuarios u ON (u.idUsuario = a.idUsuario1 OR u.idUsuario = a.idUsuario2)
        LEFT JOIN sesiones_activas sa ON sa.idUsuario = u.idUsuario
        WHERE (a.idUsuario1 = :miId OR a.idUsuario2 = :miId)
        AND a.estado = 'aceptada'
        AND u.idUsuario != :miId
    ");
        $this->db->bind(':miId', $miId);
        return $this->db->registros();
    }

    public function obtenerSolicitudesRecibidas($miId)
    {
        $this->db->query("
    SELECT a.id, u.login, u.imagen
    FROM Amistades a
    JOIN Usuarios u ON u.idUsuario = a.idUsuario1
    WHERE a.idUsuario2 = :miId AND a.estado = 'pendiente'
");
        $this->db->bind(':miId', $miId);
        return $this->db->registros();
    }

    public function aceptarSolicitudAmistad($id, $miId)
    {
        $this->db->query("UPDATE Amistades SET estado = 'aceptada' WHERE id = :id AND idUsuario2 = :miId");
        $this->db->bind(':id', $id);
        $this->db->bind(':miId', $miId);
        return $this->db->execute();
    }

    public function rechazarSolicitudAmistad($id, $miId)
    {
        $this->db->query("DELETE FROM Amistades WHERE id = :id AND idUsuario2 = :miId");
        $this->db->bind(':id', $id);
        $this->db->bind(':miId', $miId);
        return $this->db->execute();
    }

    public function eliminarAmistad($miId, $amigoId)
    {
        $this->db->query("DELETE FROM Amistades WHERE 
            (idUsuario1 = :miId AND idUsuario2 = :amigoId) 
            OR (idUsuario1 = :amigoId AND idUsuario2 = :miId)");
        $this->db->bind(':miId', $miId);
        $this->db->bind(':amigoId', $amigoId);
        return $this->db->execute();
    }

    public function obtenerPerfilPorId($id)
    {
        $this->db->query("SELECT idUsuario, login, nombreUsuario, emailUsuario, imagen FROM Usuarios WHERE idUsuario = :id");
        $this->db->bind(':id', $id);
        return $this->db->registro();
    }

    public function getAcademiasPorIdUsuario($idUsuario)
    {
        $this->db->query("
            SELECT a.*
            FROM AcademiaUsuarios au
            INNER JOIN Academias a ON au.idAcademia = a.idAcademia
            WHERE au.idUsuario = :idUsuario
        ");
        $this->db->bind(':idUsuario', $idUsuario);
        return $this->db->registros();
    }
}
