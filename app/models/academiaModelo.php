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
        $this->db->query("SELECT ur.idRol, r.nombreRol FROM UsuariosRoles ur INNER JOIN Roles r ON ur.idRol = r.idRol WHERE ur.idUsuario = :idUsuario");
        $this->db->bind(':idUsuario', $idUsuario);
        $resultado = $this->db->registro();
        return $resultado ? $resultado : null;
        //usuarios base no estan en roles, asi que si no eisten en la tabla dará null
    }

    public function obtenerUsuarioPorLogin($login)
    {
        $this->db->query("SELECT * FROM Usuarios WHERE login = :login");
        $this->db->bind(':login', $login);
        return $this->db->registro();
    }
}
