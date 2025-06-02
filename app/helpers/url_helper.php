<?php
// Helper para redireccionar a una página específica
function redireccionar($pagina) {
    header ('location: '. RUTA_URL . $pagina);
}