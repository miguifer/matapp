<?php
class DataBase
{
    private $rutaBD = 'C:/Program Files/Ampps/www/misProyectos/matapp-mvc/app/db/matapp.db';

    private $dbh;   // Database handler
    private $stmt;  // Statement para ejecutar consultas
    private $error;

    public function __construct()
    {
        $dsn = 'sqlite:' . $this->rutaBD;
        $opciones = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        );

        try {
            $this->dbh = new PDO($dsn, null, null, $opciones);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            // Verificar si la carpeta logs existe antes de escribir
            $logPath = dirname(__FILE__) . '/../logs/db_errors.log';
            if (!is_dir(dirname($logPath))) {
                mkdir(dirname($logPath), 0777, true);
            }
            error_log($this->error, 3, $logPath);
        }
    }

    public function query($sql)
    {
        if ($this->dbh) {
            $this->stmt = $this->dbh->prepare($sql);
        } else {
            die("Error de conexión a la base de datos.");
        }
    }

    public function bind($parametro, $valor, $tipo = null)
    {
        if (is_null($tipo)) {
            switch (true) {
                case is_int($valor):
                    $tipo = PDO::PARAM_INT;
                    break;
                case is_bool($valor):
                    $tipo = PDO::PARAM_BOOL;
                    break;
                case is_null($valor):
                    $tipo = PDO::PARAM_NULL;
                    break;
                default:
                    $tipo = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($parametro, $valor, $tipo);
    }

    public function execute()
    {
        return $this->stmt->execute();
    }

    public function registros()
    {
        $this->execute();
        return $this->stmt->fetchAll();
    }

    public function registro()
    {
        $this->execute();
        return $this->stmt->fetch();
    }

    public function rowCount()
    {
        return $this->stmt->rowCount();
    }

    public function close()
    {
        $this->dbh = null;
    }
}
