BEGIN TRANSACTION;
CREATE TABLE IF NOT EXISTS "AcademiaUsuarios" (
	"idInscripcion"	INTEGER NOT NULL UNIQUE,
	"idUsuario"	INTEGER NOT NULL,
	"idAcademia"	INTEGER NOT NULL,
	PRIMARY KEY("idInscripcion" AUTOINCREMENT),
	FOREIGN KEY("idAcademia") REFERENCES "Academias"("idAcademia"),
	FOREIGN KEY("idUsuario") REFERENCES "Usuarios"("idUsuario")
);
CREATE TABLE IF NOT EXISTS "Academias" (
	"idAcademia"	INTEGER NOT NULL UNIQUE,
	"nombreAcademia"	TEXT,
	"ubicacionAcademia"	TEXT,
	"tipoAcademia"	TEXT,
	"idGerente"	TEXT,
	"path_imagen"	TEXT,
	"latitud"	REAL,
	"longitud"	REAL,
	PRIMARY KEY("idAcademia" AUTOINCREMENT),
	FOREIGN KEY("idGerente") REFERENCES "Usuarios"("idUsuario"),
	FOREIGN KEY("tipoAcademia") REFERENCES "TipoAcademia"("idTipo")
);
CREATE TABLE IF NOT EXISTS "AcademiasEntrenadores" (
	"idAcademiaEntrenador"	INTEGER NOT NULL UNIQUE,
	"idACademia"	INTEGER,
	"idUsuario"	INTEGER,
	PRIMARY KEY("idAcademiaEntrenador" AUTOINCREMENT),
	FOREIGN KEY("idACademia") REFERENCES "Academias"("idAcademia"),
	FOREIGN KEY("idUsuario") REFERENCES "Usuarios"("idUsuario")
);
CREATE TABLE IF NOT EXISTS "Amistades" (
	"id"	INTEGER UNIQUE,
	"idUsuario1"	INT NOT NULL,
	"idUsuario2"	INT NOT NULL,
	"estado"	VARCHAR(20) NOT NULL,
	"fechaSolicitud"	DATETIME DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY("id" AUTOINCREMENT),
	FOREIGN KEY("idUsuario1") REFERENCES "Usuarios"("idUsuario"),
	FOREIGN KEY("idUsuario2") REFERENCES "Usuarios"("idUsuario"),
	CHECK("estado" IN ('aceptada', 'pendiente'))
);
CREATE TABLE IF NOT EXISTS "Reservas" (
	"idReserva"	INTEGER NOT NULL UNIQUE,
	"idClase"	INTEGER,
	"idUsuario"	INTEGER,
	"asistencia"	INTEGER DEFAULT 0,
	PRIMARY KEY("idReserva" AUTOINCREMENT),
	FOREIGN KEY("idClase") REFERENCES "clases"("id") ON DELETE CASCADE,
	FOREIGN KEY("idUsuario") REFERENCES "Usuarios"("idUsuario")
);
CREATE TABLE IF NOT EXISTS "Reservas_backup" (
	"idReserva"	INTEGER NOT NULL UNIQUE,
	"idClase"	INTEGER,
	"idUsuario"	INTEGER,
	"asistencia"	INTEGER DEFAULT 0,
	PRIMARY KEY("idReserva" AUTOINCREMENT),
	FOREIGN KEY("idClase") REFERENCES "clases"("id"),
	FOREIGN KEY("idUsuario") REFERENCES "Usuarios"("idUsuario")
);
CREATE TABLE IF NOT EXISTS "Roles" (
	"idRol"	INTEGER NOT NULL UNIQUE,
	"nombreRol"	TEXT NOT NULL,
	PRIMARY KEY("idRol" AUTOINCREMENT)
);
CREATE TABLE IF NOT EXISTS "Solicitudes" (
	"idSolicitud"	INTEGER NOT NULL UNIQUE,
	"idUsuario"	INTEGER NOT NULL,
	"idAcademia"	INTEGER NOT NULL,
	"estadoSolicitud"	TEXT NOT NULL,
	"fechaSolicitud"	TEXT NOT NULL,
	PRIMARY KEY("idSolicitud" AUTOINCREMENT),
	FOREIGN KEY("idAcademia") REFERENCES "Academias"("idAcademia"),
	FOREIGN KEY("idUsuario") REFERENCES "Usuarios"("idUsuario"),
	CHECK("estadoSolicitud" IN ('aceptada', 'rechazada', 'pendiente'))
);
CREATE TABLE IF NOT EXISTS "TipoAcademia" (
	"idTipo"	INTEGER NOT NULL UNIQUE,
	"nombreTipo"	TEXT NOT NULL UNIQUE,
	PRIMARY KEY("idTipo" AUTOINCREMENT)
);
CREATE TABLE IF NOT EXISTS "Usuarios" (
	"idUsuario"	INTEGER NOT NULL UNIQUE,
	"login"	TEXT NOT NULL UNIQUE,
	"password"	TEXT NOT NULL,
	"emailUsuario"	TEXT NOT NULL UNIQUE,
	"telefonoUsuario"	TEXT,
	"nombreUsuario"	TEXT,
	"apellido1Usuario"	TEXT,
	"apellido2Usuario"	TEXT,
	"activo"	INTEGER,
	"token"	TEXT,
	"imagen"	BLOB,
	PRIMARY KEY("idUsuario" AUTOINCREMENT)
);
CREATE TABLE IF NOT EXISTS "UsuariosRoles" (
	"idUsuarioRol"	INTEGER NOT NULL UNIQUE,
	"idUsuario"	INTEGER NOT NULL,
	"idRol"	INTEGER NOT NULL,
	PRIMARY KEY("idUsuarioRol" AUTOINCREMENT),
	FOREIGN KEY("idRol") REFERENCES "Roles"("idRol"),
	FOREIGN KEY("idUsuario") REFERENCES "Usuarios"("idUsuario")
);
CREATE TABLE IF NOT EXISTS "clases" (
	"id"	INTEGER NOT NULL UNIQUE,
	"title"	TEXT NOT NULL,
	"start"	TEXT NOT NULL,
	"end"	TEXT,
	"idAcademia"	INTEGER NOT NULL,
	"idEntrenador"	INTEGER,
	PRIMARY KEY("id" AUTOINCREMENT),
	FOREIGN KEY("idAcademia") REFERENCES "Academias"("idAcademia"),
	FOREIGN KEY("idEntrenador") REFERENCES "Usuarios"("idUsuario")
);
CREATE TABLE IF NOT EXISTS "muro_mensajes" (
	"idMensaje"	INTEGER NOT NULL UNIQUE,
	"idAcademia"	INTEGER,
	"idUsuario"	INTEGER,
	"mensaje"	TEXT,
	"fecha"	TEXT,
	"fijado"	INTEGER,
	PRIMARY KEY("idMensaje" AUTOINCREMENT),
	FOREIGN KEY("idAcademia") REFERENCES "Academias"("idAcademia"),
	FOREIGN KEY("idUsuario") REFERENCES "Usuarios"("idUsuario")
);
CREATE TABLE IF NOT EXISTS "sesiones_activas" (
	"idUsuario"	INT,
	"last_activity"	DATETIME NOT NULL,
	PRIMARY KEY("idUsuario")
);
COMMIT;
