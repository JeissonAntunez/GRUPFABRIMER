<?php
$peticionAjax = true;
require_once "../../config/app.php";
require_once "../views/inc/session_start.php";
require_once "../../autoload.php";

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
        $idClase = intval($_POST['id_clase'] ?? 0);
        $idTienda = intval($_POST['id_tienda'] ?? 0);
        $busqueda = $_POST['busqueda'] ?? '';

        // ⭐ CARGAR CACHE DE LISTAS PARA TRADUCCIÓN
        $cacheListas = [];
        if ($idTienda > 0) {
            try {
                require_once "../models/listaModel.php";
                $listaModel = new app\models\listaModel();

                $sqlListas = "SELECT VCH_CODIGO, VCH_DESCRIPCION FROM listas WHERE NUM_ID_TIENDA = :tienda AND VCH_ESTADO = 1";
                $stmtListas = $listaModel->conectar()->prepare($sqlListas);
                $stmtListas->bindParam(":tienda", $idTienda, PDO::PARAM_INT);
                $stmtListas->execute();

                while ($lista = $stmtListas->fetch()) {
                    $cacheListas[$lista['VCH_CODIGO']] = $lista['VCH_DESCRIPCION'];
                }
            } catch (Exception $e) {
                error_log("Error cargando cache listas: " . $e->getMessage());
            }
        }

        // Obtener productos (solo por clase)
        $productos = $insProducto->listarProductosControlador($idClase, $idTienda, $busqueda);
        $data = [];
        while ($row = $productos->fetch()) {
            // ⭐ TRADUCIR CÓDIGOS A DESCRIPCIONES
            foreach ($row as $key => $valor) {
                if (!empty($valor) && is_string($valor) && isset($cacheListas[$valor])) {
                    $row[$key] = $cacheListas[$valor];
                }
            }
            $data[] = $row;
        }

        // Obtener headers dinámicos de plantilla
        $headers = [];

        if ($idClase > 0 && $idTienda > 0) {
            require_once "../models/plantillaModel.php";
            $plantillaModel = new app\models\plantillaModel();

            // Misma consulta que usas para el Excel
            $sql = "SELECT NUM_ID_PLANTILLA FROM plantilla 
                    WHERE NUM_ID_CLASE = :clase 
                    AND NUM_ID_TIENDA = :tienda 
                    AND VCH_ESTADO = 1 
                    LIMIT 1";

            $stmt = $plantillaModel->conectar()->prepare($sql);
            $stmt->bindParam(':clase', $idClase, PDO::PARAM_INT);
            $stmt->bindParam(':tienda', $idTienda, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $plantilla = $stmt->fetch();
                $idPlantilla = $plantilla['NUM_ID_PLANTILLA'];

                // Obtener columnas ordenadas
                $sqlDetalle = "SELECT VCH_CAMPO, VCH_NOMBRE_PLANTILLA, NUM_ORDEN 
                               FROM plant_detalle 
                               WHERE NUM_ID_PLANTILLA = :id 
                               AND VCH_ESTADO = 1 
                               ORDER BY NUM_ORDEN ASC";

                $stmtDetalle = $plantillaModel->conectar()->prepare($sqlDetalle);
                $stmtDetalle->bindParam(':id', $idPlantilla, PDO::PARAM_INT);
                $stmtDetalle->execute();

                while ($detalle = $stmtDetalle->fetch()) {
                    $headers[] = [
                        'nombre' => $detalle['VCH_CAMPO'],                    // Para el <th>
                        'campo' => $detalle['VCH_NOMBRE_PLANTILLA'],          // Para obtener el dato
                        'orden' => $detalle['NUM_ORDEN']
                    ];
                }
            }
        }

        echo json_encode([
            'status' => 'ok',
            'data' => $data,
            'headers' => $headers,
            'tiene_plantilla' => !empty($headers)
        ]);
        exit;
    }

    if ($modulo == "obtener_plantilla_vacia") {
        $insProducto->obtenerPlantillaVaciaControlador();
    }

    if ($modulo == "obtener_plantilla_excel") {
        $insProducto->obtenerPlantillaExcelControlador();
    }

    if ($modulo == "importar_csv") {
        echo $insProducto->importarCSVControlador();
    }

    if ($modulo == "estadisticas") {
        echo $insProducto->obtenerEstadisticasControlador();
    }
}
