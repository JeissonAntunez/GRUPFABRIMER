<?php
$peticionAjax = true;
require_once "../../config/app.php";
require_once "../views/inc/session_start.php";
require_once "../../autoload.php";

if (isset($_POST['modulo_plantilla'])) {
    require_once "../controllers/plantillaController.php";
    $insPlantilla = new app\controllers\plantillaController();


    if ($_POST['modulo_plantilla'] == "registrar_plantilla") {
        echo $insPlantilla->registrarPlantillaControlador();
    }

    if ($_POST['modulo_plantilla'] == "actualizar_plantilla") {
        echo $insPlantilla->actualizarPlantillaControlador();
    }

    if ($_POST['modulo_plantilla'] == "eliminar_plantilla") {
        echo $insPlantilla->eliminarPlantillaControlador();
    }

    if ($_POST['modulo_plantilla'] == "actualizar_estado_plantilla") {
        echo $insPlantilla->actualizarEstadoPlantillaControlador();
    }

    if ($_POST['modulo_plantilla'] == "listar_plantillas") {
        $idClase = $_POST['f_clase'] ?? 0;
        $idTienda = $_POST['f_tienda'] ?? 0;

        $plantillas = $insPlantilla->listarPlantillasControlador($idClase, $idTienda);

        $data = [];
        while ($row = $plantillas->fetch()) {
            $data[] = $row;
        }

        echo json_encode(['status' => 'ok', 'data' => $data], JSON_UNESCAPED_UNICODE);
    }

   
    if ($_POST['modulo_plantilla'] == "registrar_detalle") {
        echo $insPlantilla->registrarDetalleControlador();
    }

    if ($_POST['modulo_plantilla'] == "actualizar_detalle") {
        echo $insPlantilla->actualizarDetalleControlador();
    }

    if ($_POST['modulo_plantilla'] == "eliminar_detalle") {
        echo $insPlantilla->eliminarDetalleControlador();
    }

    if ($_POST['modulo_plantilla'] == "actualizar_estado_detalle") {
        echo $insPlantilla->actualizarEstadoDetalleControlador();
    }

    if ($_POST['modulo_plantilla'] == "listar_detalles") {
        $idPlantilla = $_POST['id_plantilla'] ?? 0;
        $idClase = $_POST['f_clase'] ?? 0;
        $idTienda = $_POST['f_tienda'] ?? 0;

        $detalles = $insPlantilla->listarDetallesControlador($idPlantilla, $idClase, $idTienda);

        $data = [];
        while ($row = $detalles->fetch()) {
            $data[] = $row;
        }

        echo json_encode(['status' => 'ok', 'data' => $data], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'msg' => 'Módulo no especificado'
    ], JSON_UNESCAPED_UNICODE);
}
