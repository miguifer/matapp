<?php

use eftec\bladeone\BladeOne;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();
class Paginas extends Controlador
{

    private $usuarioModelo;
    private $blade;
    private $views = __DIR__ . '/../views';
    private $cache = __DIR__ . '/../cache';

    public function __construct()
    {
        //1) Acceso al modelo
        $this->usuarioModelo = $this->modelo('Usuario');
    }

    public function index()
    {


        //RESEND EJEMPLO

        // $resend = Resend::client($_ENV['RESEND_API_KEY']);

        // $resend->emails->send([
        //     'from' => 'onboarding@resend.dev',
        //     'to' => 'mfernandezfie@gmail.com',
        //     'subject' => 'Hello World',
        //     'html' => '<p>Has iniciado la página</p>'
        // ]);



        // Podemos pasar parametros a la vista que queramos
        // Para ello nos creamos un array con los parámetros
        $usuarios = $this->usuarioModelo->obtenerUsuarios();
        $datos = [
            'usuarios' => $usuarios, // Array con todos los usuarios
            'name' => 'Miguel',
            'ejemplo' => $_ENV['EJEMPLO'] ?? 'Valor por defecto'
        ];
        // Le pasamos a la vista los parametros
        // Renderiza la vista "welcome" pasando datos

        $this->blade = new BladeOne($this->views, $this->cache, BladeOne::MODE_AUTO);
        echo $this->blade->run("welcome", $datos);
    }

    public function calendario()
    {
        $datos = [
            'todayDate' => new DateTime()
        ];

        $this->blade = new BladeOne($this->views, $this->cache, BladeOne::MODE_AUTO);
        echo $this->blade->run("calendario", $datos);
    }
}
