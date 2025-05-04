
<?php

class Core {
    
    const PATH_CONTROLADORES =  "../app/controllers/";

    protected $controladorActual = 'home'; 
    protected $metodoActual = 'index';
    protected $parametros =[];

    public function __construct(){
        $url = $this->getURL();
        if (!is_null ($url) && file_exists(self::PATH_CONTROLADORES . ucwords($url[0]). '.php')) { 
            $this->controladorActual = ucwords($url[0]);
            unset($url[0]);
        }
        $file = self::PATH_CONTROLADORES . $this->controladorActual . '.php';
        require_once $file;

        
        $this->controladorActual = new $this->controladorActual(); 

        if (isset($url[1])) {
            if (method_exists($this->controladorActual, $url[1])) {
                $this->metodoActual=$url[1];
                unset($url[1]); 
            } 
        }
        $this->parametros = $url?array_values($url):["nada"];
        call_user_func_array([$this->controladorActual, $this->metodoActual], $this->parametros);
    }

    public function getURL() {
        if (isset($_GET['url'])) { 
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url); 
            return $url;
        }
    }
    
}

