<?php

// Cargar clases de Blade y dotenv
use eftec\bladeone\BladeOne;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

// Controlador de Academia
class academia extends Controlador
{

    private $academiaModelo;
    private $blade;
    private $views = __DIR__ . '/../views';
    private $cache = __DIR__ . '/../cache';

    public function __construct()
    {
        $this->academiaModelo = $this->modelo('academiaModelo');
    }

    public function index()
    {

        $academia = isset($_GET['academia']) ? json_decode(urldecode($_GET['academia'])) : null;
        $usuario = isset($_SESSION['userLogin']['usuario']) ? json_decode($_SESSION['userLogin']['usuario']) : null;

        // Verificar rol del usuario logueado
        $esAdmin = $usuario->rol == 'Administrador' ? true : false;
        $esAlumno = $this->academiaModelo->esAlumno($academia->idAcademia, $usuario->idUsuario);
        $esGerente = $academia->idGerente == $usuario->idUsuario;
        $esEntrenador = $this->academiaModelo->esEntrenador($academia->idAcademia, $usuario->idUsuario);


        if ($academia == null) {
            redireccionar('/');
        } elseif ($usuario == null) {
            redireccionar('/inicioSesion?academia=' . urlencode(json_encode($academia)));
        } else {

            $datos = [
                'academia' => $academia,
            ];

            // Dependiendo del rol del usuario, se obtienen diferentes datos de la academia
            if ($esAdmin) {
                $estadisticaAcademia = $this->academiaModelo->obtenerEstadisticaAcademias();
                $solicitudes = $this->academiaModelo->obtenerSolicitudesAcademia($academia->idAcademia);
                $alumnos = $this->academiaModelo->obtenerAlumnosAcademia($academia->idAcademia);

                foreach ($alumnos as &$alumno) {
                    if (isset($alumno->imagen) && !empty($alumno->imagen)) {
                        $alumno->imagen = 'data:image/jpeg;base64,' . base64_encode($alumno->imagen);
                    }
                }
                $datos['alumnos'] = $alumnos;
                $datos['estadisticaAcademia'] = $estadisticaAcademia;
                foreach ($solicitudes as &$solicitud) {
                    if (isset($solicitud->imagen) && !empty($solicitud->imagen)) {
                        $solicitud->imagen = 'data:image/jpeg;base64,' . base64_encode($solicitud->imagen);
                    }
                }
                $datos['solicitudes'] = $solicitudes;
                $clases = $this->academiaModelo->obtenerClasesAcademia($academia->idAcademia);
                foreach ($clases as &$clase) {
                    if (isset($clase->imagen) && !empty($clase->imagen)) {
                        $clase->imagen = 'data:image/jpeg;base64,' . base64_encode($clase->imagen);
                    }
                }
                $datos['clases'] = $clases;

                $usuario->rol = 'Administrador';
                $_SESSION['userLogin'] = [
                    'usuario' => json_encode($usuario),
                ];
            } else if ($esGerente) {
                $estadisticaAcademia = $this->academiaModelo->obtenerEstadisticaAcademias();
                $solicitudes = $this->academiaModelo->obtenerSolicitudesAcademia($academia->idAcademia);
                $alumnos = $this->academiaModelo->obtenerAlumnosAcademia($academia->idAcademia);

                foreach ($alumnos as &$alumno) {
                    if (isset($alumno->imagen) && !empty($alumno->imagen)) {
                        $alumno->imagen = 'data:image/jpeg;base64,' . base64_encode($alumno->imagen);
                    }
                }
                $datos['alumnos'] = $alumnos;
                $datos['estadisticaAcademia'] = $estadisticaAcademia;
                foreach ($solicitudes as &$solicitud) {
                    if (isset($solicitud->imagen) && !empty($solicitud->imagen)) {
                        $solicitud->imagen = 'data:image/jpeg;base64,' . base64_encode($solicitud->imagen);
                    }
                }
                $datos['solicitudes'] = $solicitudes;
                $clases = $this->academiaModelo->obtenerClasesAcademia($academia->idAcademia);
                foreach ($clases as &$clase) {
                    if (isset($clase->imagen) && !empty($clase->imagen)) {
                        $clase->imagen = 'data:image/jpeg;base64,' . base64_encode($clase->imagen);
                    }
                }
                $datos['clases'] = $clases;


                $usuario->rol = 'Gerente';
                $_SESSION['userLogin'] = [
                    'usuario' => json_encode($usuario),
                ];
            } elseif ($esEntrenador) {
                $estadisticaAcademia = $this->academiaModelo->obtenerEstadisticaAcademias();
                $datos['estadisticaAcademia'] = $estadisticaAcademia;
                $clases = $this->academiaModelo->obtenerClasesAcademia($academia->idAcademia);
                foreach ($clases as &$clase) {
                    if (isset($clase->imagen) && !empty($clase->imagen)) {
                        $clase->imagen = 'data:image/jpeg;base64,' . base64_encode($clase->imagen);
                    }
                }
                $datos['clases'] = $clases;
                $usuario->rol = 'Entrenador';
                $_SESSION['userLogin'] = [
                    'usuario' => json_encode($usuario),
                ];
            } elseif ($esAlumno) {
                $estadisticaAcademia = $this->academiaModelo->obtenerEstadisticaAcademias();
                $datos['estadisticaAcademia'] = $estadisticaAcademia;

                $usuario->rol = 'Alumno';
                $_SESSION['userLogin'] = [
                    'usuario' => json_encode($usuario),
                ];
            } else {

                $usuario->rol = 'Cliente';
                $_SESSION['userLogin'] = [
                    'usuario' => json_encode($usuario),
                ];

                redireccionar('/academia/solicitarAcceso?academia=' . urlencode(json_encode($academia)));
            }

            // start Datos comunes a todos los roles
            $entrenadores = $this->academiaModelo->obtenerEntrenadoresAcademia($academia->idAcademia) ?? [];
            $mensajes = $this->academiaModelo->obtenerMensajesAcademia($academia->idAcademia);
            $ranking = $this->academiaModelo->getRankingAsistencia($academia->idAcademia);

            foreach ($entrenadores as &$entrenador) {
                if (isset($entrenador->imagen) && !empty($entrenador->imagen)) {
                    $entrenador->imagen = 'data:image/jpeg;base64,' . base64_encode($entrenador->imagen);
                }
            }
            $datos['entrenadores'] = $entrenadores;
            foreach ($mensajes as &$mensaje) {
                if (isset($mensaje->imagen) && !empty($mensaje->imagen)) {
                    $mensaje->imagen = 'data:image/jpeg;base64,' . base64_encode($mensaje->imagen);
                }
            }
            $datos['mensajes'] = $mensajes;
            foreach ($ranking as &$usuarioRanking) {
                if (isset($usuarioRanking->imagen) && !empty($usuarioRanking->imagen)) {
                    $usuarioRanking->imagen = 'data:image/jpeg;base64,' . base64_encode($usuarioRanking->imagen);
                }
            }
            $datos['ranking'] = $ranking;
            // stop Datos comunes a todos los roles

            $this->blade = new BladeOne($this->views, $this->cache, BladeOne::MODE_AUTO);
            echo $this->blade->run("academia.inicio", $datos);
        }
    }

    /**
     * Método para solicitar acceso a una academia.
     * Si el usuario ya tiene una solicitud en curso, redirige con un mensaje de error.
     * Si no, crea una nueva solicitud y redirige con un mensaje de éxito.
     */
    public function solicitarAcceso()
    {

        $academia = isset($_GET['academia']) ? json_decode(urldecode($_GET['academia'])) : null;
        $usuario = isset($_SESSION['userLogin']['usuario']) ? json_decode($_SESSION['userLogin']['usuario']) : null;


        if ($usuario == null) {
            redireccionar('/');
        }
        if ($academia == null) {
            redireccionar('/');
        }



        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

            $datos = [
                'academia' => $academia,
            ];

            $this->blade = new BladeOne($this->views, $this->cache, BladeOne::MODE_AUTO);
            echo $this->blade->run("academia.solicitarAcceso", $datos);
        } else {
            if (isset($_POST['idAcademia']) && isset($_POST['idUsuario'])) {

                $solicitudEnCurso = $this->academiaModelo->obtenerSolicitudEnCurso($_POST['idUsuario'], $_POST['idAcademia']) ? true : false;
                if ($solicitudEnCurso) {

                    redireccionar('?toastrErr=Ya tienes una solicitud en curso para esta academia');
                } else {

                    $this->academiaModelo->crearSolicitud($_POST['idUsuario'], $_POST['idAcademia']);
                    redireccionar('?toastrErr=Solicitud enviada correctamente, espera la respuesta del administrador de la academia');
                }
            } else {
                redireccionar('/');
            }
        }
    }



    // Método para subir una foto a la galería de la academia
    // Pone la foto en la carpeta pública data/academias-gallery/{idAcademia}
    public function subirFoto()
    {
        $usuario = isset($_SESSION['userLogin']['usuario']) ? json_decode($_SESSION['userLogin']['usuario']) : null;
        if (!$usuario || !isset($_POST['idAcademia'])) {
            redireccionar('/');
        }

        $idAcademia = $_POST['idAcademia'];

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $dir = __DIR__ . "/../../public/data/academias-gallery/$idAcademia";
            if (!is_dir($dir)) mkdir($dir, 0777, true);

            $nombreArchivo = uniqid() . '_' . basename($_FILES['foto']['name']);
            $rutaDestino = $dir . '/' . $nombreArchivo; // Ruta física
            move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino);
            redireccionar("?toastrErr=Foto subida correctamente");
        } else {
            redireccionar("?toastrErr=Error al subir la foto");
        }
    }

    // Método para editar la información de la academia
    public function editarInfo()
    {
        header('Content-Type: application/json');
        $usuario = isset($_SESSION['userLogin']['usuario']) ? json_decode($_SESSION['userLogin']['usuario']) : null;
        if (!$usuario || !isset($_POST['idAcademia'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Usuario no autenticado o academia no especificada']);
            exit;
        }

        $idAcademia = $_POST['idAcademia'];
        $nombreAcademia = isset($_POST['nombre']) ? trim($_POST['nombre']) : null;
        $ubicacionAcademia = isset($_POST['ubicacion']) ? trim($_POST['ubicacion']) : null;

        if ($nombreAcademia && $ubicacionAcademia) {
            $this->academiaModelo->actualizarInfoAcademia($idAcademia, $nombreAcademia, $ubicacionAcademia);
            echo json_encode(['success' => true, 'message' => 'Información actualizada correctamente']);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Faltan datos para actualizar la información']);
        }
    }
}
