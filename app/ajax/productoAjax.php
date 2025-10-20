<?php
$peticionAjax = true;
require_once "../../config/app.php";
require_once "../views/inc/session_start.php";
require_once "../../autoload.php";

// Detectar módulo desde POST o GET
$modulo = $_POST['modulo_producto'] ?? $_GET['modulo_producto'] ?? null;

if ($modulo) {
    require_once "../controllers/productoController.php";
    $insProducto = new app\controllers\productoController();

    if ($modulo == "registrar") {
        echo $insProducto->registrarProductoControlador();
    }

    if ($modulo == "actualizar") {
        echo $insProducto->actualizarProductoControlador();
    }

    if ($modulo == "eliminar") {
        echo $insProducto->eliminarProductoControlador();
    }

    if ($modulo == "listar") {
        $idClase = $_POST['id_clase'] ?? 0;
        $idTienda = $_POST['id_tienda'] ?? 0;
        $busqueda = $_POST['busqueda'] ?? '';

        $productos = $insProducto->listarProductosControlador($idClase, $idTienda, $busqueda);

        $data = [];
        while ($row = $productos->fetch()) {
            $data[] = $row;
        }

        echo json_encode(['status' => 'ok', 'data' => $data]);
    }

    // ⭐ Plantilla vacía (soporta GET)
    if ($modulo == "obtener_plantilla_vacia") {
        $insProducto->obtenerPlantillaVaciaControlador(); // Ya hace exit interno
    }

    // ⭐ Plantilla específica (soporta GET)
    if ($modulo == "obtener_plantilla_excel") {
        $insProducto->obtenerPlantillaExcelControlador(); // Ya hace exit interno
    }

    if ($modulo == "importar_csv") {
        echo $insProducto->importarCSVControlador();
    }

    if ($modulo == "estadisticas") {
        echo $insProducto->obtenerEstadisticasControlador();
    }
}
