<?php

// BASE DE DATOS
class DataBase
{
    private $rutaBD = RUTA_APP . '/db/matapp.db';

    private $dbh;
    private $stmt;

    // Conexión a la base de datos
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
            $this->logError($e->getMessage());
        }
    }

    // prepara una consulta SQL
    // $sql: consulta SQL a preparar
    public function query($sql)
    {
        try {
            if ($this->dbh) {
                $this->stmt = $this->dbh->prepare($sql);
            } else {
                throw new Exception("Error de conexión a la base de datos.");
            }
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            die("Error de base de datos. Consulta el log.");
        }
    }

    // vincula un parámetro a la consulta preparada
    // $parametro: nombre del parámetro (ej. :id)
    // $valor: valor a vincular al parámetro
    public function bind($parametro, $valor, $tipo = null)
    {
        try {
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
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            die("Error de base de datos. Consulta el log.");
        }
    }

    // ejecuta la consulta preparada
    public function execute()
    {
        try {
            return $this->stmt->execute();
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            die("Error de base de datos. Consulta el log.");
        }
    }

    // obtiene todos los registros de la consulta
    public function registros()
    {
        try {
            $this->execute();
            return $this->stmt->fetchAll();
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            die("Error de base de datos. Consulta el log.");
        }
    }

    // obtiene un único registro de la consulta
    public function registro()
    {
        try {
            $this->execute();
            return $this->stmt->fetch();
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            die("Error de base de datos. Consulta el log.");
        }
    }

    // obtiene el numero de filas afectadas por la última consulta
    public function rowCount()
    {
        try {
            return $this->stmt->rowCount();
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            die("Error de base de datos. Consulta el log.");
        }
    }

    private function logError($mensaje)
    {
        $logPath = dirname(__FILE__) . '/../logs/db_errors.log';
        if (!is_dir(dirname($logPath))) {
            mkdir(dirname($logPath), 0777, true);
        }
        error_log(date('[Y-m-d H:i:s] ') . $mensaje . PHP_EOL, 3, $logPath);
    }

    // cierra la conexión a la base de datos
    public function close()
    {
        $this->dbh = null;
    }
}
