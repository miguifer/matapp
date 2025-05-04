<?php
class Controlador {
    public function modelo($m) {
        require_once '../app/models/' . $m . '.php';
        return new $m();
    }

     public function vista($v, $datos=[]) {        
        if (file_exists('../app/views/' . $v . '.php')) {
            require_once '../app/views/' . $v . '.php';           
        } else {
            die ('La vista no existe');
        }
  
    }

}