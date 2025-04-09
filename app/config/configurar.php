<?php
// Configuracion de acceso a la base de datos
define('DB_HOST', 'localhost');
define('DB_NOMBRE', 'matapp');

// Ruta de la aplicación raiz /localhost/DWES/mvc
// Con __FILE__ Obtenemos la ruta de este archivo 'configurar.php'
// Con dirname obtenemos la carpeta padre de un ruta determinada
define('RUTA_APP', dirname(dirname(__FILE__)));

// Ruta URL 
// Similar a la ruta PATH pero partiendo del dominio
define('RUTA_URL', 'http://localhost/matapp');

// Nombre del sitio
define('NOMBRE_SITIO', 'matapp');
