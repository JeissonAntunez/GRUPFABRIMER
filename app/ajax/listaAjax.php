<?php

require_once "../../config/app.php";
require_once "../views/inc/session_start.php";
require_once "../../autoload.php";

use app\controllers\listaController;

if (isset($_POST['modulo_lista'])) {

    $insLista = new listaController();

    if ($_POST['modulo_lista'] == "get-next-id") {
        echo $insLista->obtenerSiguienteIdControlador();
    }

    if ($_POST['modulo_lista'] == "registrar") {
        echo $insLista->registrarListaControlador();
    }

    if ($_POST['modulo_lista'] == "actualizar") {
        echo $insLista->actualizarListaControlador();
    }

    if ($_POST['modulo_lista'] == "actualizar_estado") {
        echo $insLista->actualizarEstadoControlador();
    }

    if ($_POST['modulo_lista'] == "eliminar") {
        echo $insLista->eliminarListaControlador();
    }
} else {
    session_destroy();
    header("Location: " . APP_URL . "login/");
}
