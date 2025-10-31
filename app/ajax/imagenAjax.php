<?php

$peticionAjax = true;
require_once "../../config/app.php";
require_once "../views/inc/session_start.php";
require_once "../../autoload.php";

use app\controllers\imagenController;


if (!isset($_SESSION['usuario'])) {
    echo json_encode([
        "status" => "error",
        "msg" => "Sesión no válida. Por favor inicie sesión."
    ]);
    exit;
}


$imagenController = new imagenController();


$modulo = $_GET['modulo_imagen'] ?? $_POST['modulo_imagen'] ?? '';


switch ($modulo) {

    case 'cargar':
  
        echo $imagenController->registrarImagenControlador();
        break;

    case 'cargar_excel':
  
        echo $imagenController->cargarImagenesExcelControlador();
        break;

    case 'listar':

        echo $imagenController->listarImagenesControlador();
        break;

    case 'eliminar':
 
        echo $imagenController->eliminarImagenControlador();
        break;

    case 'buscar_sku':
        
        echo $imagenController->buscarSkuPadreControlador();
        break;

    default:
        echo json_encode([
            "status" => "error",
            "msg" => "Módulo '$modulo' no encontrado o no especificado"
        ]);
        break;
}