
<?php

// Este archivo es parte del núcleo de la aplicación MVC.
// Su función principal es gestionar la carga de controladores y métodos según la URL solicitada.
class Core
{

    const PATH_CONTROLADORES =  "../app/controllers/";

    // Controlador método y parámetros por defecto
    protected $controladorActual = 'home';
    protected $metodoActual = 'index';
    protected $parametros = [];

    /**
     * Inicializa el controlador actual, el método actual y los parámetros.
     * Carga el controlador correspondiente según la URL solicitada.
     * Permite con el uso de la URL amigable acceder a diferentes controladores y sus métodos.
     * En formato: /controlador/metodo/parametros
     */
    public function __construct()
    {
        $url = $this->getURL();
        if (!is_null($url) && file_exists(self::PATH_CONTROLADORES . ucwords($url[0]) . '.php')) {
            $this->controladorActual = ucwords($url[0]);
            unset($url[0]);
        }
        $file = self::PATH_CONTROLADORES . $this->controladorActual . '.php';
        require_once $file;


        $this->controladorActual = new $this->controladorActual();

        if (isset($url[1])) {
            if (method_exists($this->controladorActual, $url[1])) {
                $this->metodoActual = $url[1];
                unset($url[1]);
            }
        }
        
        $this->parametros = $url ? array_values($url) : ["nada"];
        call_user_func_array([$this->controladorActual, $this->metodoActual], $this->parametros);
    }

    /**
     * Obtiene la URL de la petición y la procesa.
     * 
     * @return array|null Devuelve un array con los segmentos de la URL o null si no hay URL.
     */
    public function getURL()
    {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
    }
}
