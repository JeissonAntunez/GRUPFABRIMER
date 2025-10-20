<?php

$peticionAjax = true;
require_once "../../config/app.php";
require_once "../views/inc/session_start.php";
require_once "../../autoload.php";

use app\controllers\imagenController;

// Verificar sesión
if (!isset($_SESSION['usuario'])) {
    echo json_encode([
        "status" => "error",
        "msg" => "Sesión no válida. Por favor inicie sesión."
    ]);
    exit;
}

// Instanciar controlador
$imagenController = new imagenController();

// Obtener módulo
$modulo = $_GET['modulo_imagen'] ?? $_POST['modulo_imagen'] ?? '';

// Manejo de rutas
switch ($modulo) {

    case 'cargar':
        // Registrar/Cargar imagen individual
        echo $imagenController->registrarImagenControlador();
        break;

    case 'cargar_excel':
        // Cargar desde Excel
        echo $imagenController->cargarImagenesExcelControlador();
        break;

    case 'listar':
        // Listar imágenes de un SKU padre
        echo $imagenController->listarImagenesControlador();
        break;

    case 'eliminar':
        // Eliminar imagen (limpiar campo)
        echo $imagenController->eliminarImagenControlador();
        break;

    case 'buscar_sku':
        // Buscar información del SKU padre
        echo $imagenController->buscarSkuPadreControlador();
        break;

    default:
        echo json_encode([
            "status" => "error",
            "msg" => "Módulo '$modulo' no encontrado o no especificado"
        ]);
        break;
}