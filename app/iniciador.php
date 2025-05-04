<?php
session_start();

require_once 'config/configurar.php';

require_once 'helpers/url_helper.php';

require_once 'helpers/helper.php';

require '../vendor/autoload.php';

spl_autoload_register(function ($nombreClase) {
    require_once 'librerias/' . $nombreClase . '.php';
});




