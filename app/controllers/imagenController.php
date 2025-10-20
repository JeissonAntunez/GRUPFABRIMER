<?php

namespace app\controllers;

use app\models\imagenModel;
use app\models\productoModel;

class imagenController extends mainController
{
    private $imagenModel;
    private $productoModel;

    public function __construct()
    {
        $this->imagenModel = new imagenModel();
        $this->productoModel = new productoModel();
    }

    /*---------- Registrar imagen individual ----------*/
    public function registrarImagenControlador()
    {
        // Obtener datos
        $sku_padre = $this->limpiarCadena($_POST['VCH_SKU_PADRE']);
        $campo_imagen = $this->limpiarCadena($_POST['CAMPO_IMAGEN']);

        // Log para debugging
        error_log("=== INICIO CARGA DE IMAGEN ===");
        error_log("SKU Padre: " . $sku_padre);
        error_log("Campo: " . $campo_imagen);

        // Validaciones básicas
        if (empty($sku_padre) || !isset($_FILES['VCH_IMAGEN'])) {
            return json_encode([
                "status" => "error",
                "msg" => "El SKU padre y la imagen son obligatorios"
            ]);
        }

        if (empty($campo_imagen)) {
            return json_encode([
                "status" => "error",
                "msg" => "Debe seleccionar el campo de imagen"
            ]);
        }

        // Validar SKU padre existe
        $check_sku = $this->imagenModel->buscarProductoPorSkuPadre($sku_padre);
        if ($check_sku->rowCount() == 0) {
            return json_encode([
                "status" => "error",
                "msg" => "El SKU padre '$sku_padre' no existe en el sistema"
            ]);
        }

        // Validar archivo
        $archivo = $_FILES['VCH_IMAGEN'];
        
        error_log("Archivo recibido: " . print_r($archivo, true));

        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            $errores = [
                UPLOAD_ERR_INI_SIZE => 'El archivo supera upload_max_filesize en php.ini',
                UPLOAD_ERR_FORM_SIZE => 'El archivo supera MAX_FILE_SIZE',
                UPLOAD_ERR_PARTIAL => 'El archivo se subió parcialmente',
                UPLOAD_ERR_NO_FILE => 'No se subió ningún archivo',
                UPLOAD_ERR_NO_TMP_DIR => 'Falta carpeta temporal',
                UPLOAD_ERR_CANT_WRITE => 'Error al escribir en disco',
                UPLOAD_ERR_EXTENSION => 'Una extensión de PHP detuvo la subida'
            ];
            
            return json_encode([
                "status" => "error",
                "msg" => "Error al subir archivo: " . ($errores[$archivo['error']] ?? "Error desconocido #{$archivo['error']}")
            ]);
        }

        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $extensionesPermitidas)) {
            return json_encode([
                "status" => "error",
                "msg" => "Solo se permiten: " . implode(', ', $extensionesPermitidas)
            ]);
        }

        if ($archivo['size'] > 5242880) { // 5MB
            return json_encode([
                "status" => "error",
                "msg" => "El archivo no debe superar 5MB. Tamaño actual: " . round($archivo['size']/1048576, 2) . "MB"
            ]);
        }

        // CALCULAR RUTAS ABSOLUTAS
        // Obtener la ruta raíz del proyecto (donde está index.php)
        $raizProyecto = dirname(dirname(__DIR__)); // Sube 2 niveles desde app/controllers
        
        // Carpeta física (absoluta) donde se guardará
        $carpetaFisica = $raizProyecto . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'imgs' . DIRECTORY_SEPARATOR . 'productos' . DIRECTORY_SEPARATOR;
        
        // Ruta relativa para guardar en BD (accesible desde navegador)
        $carpetaRelativa = 'public/imgs/productos/';

        error_log("Raíz del proyecto: " . $raizProyecto);
        error_log("Carpeta física: " . $carpetaFisica);
        error_log("Carpeta relativa: " . $carpetaRelativa);

        // Crear carpeta si no existe
        if (!is_dir($carpetaFisica)) {
            error_log("Carpeta no existe, creándola...");
            if (!mkdir($carpetaFisica, 0777, true)) {
                error_log("ERROR: No se pudo crear la carpeta");
                return json_encode([
                    "status" => "error",
                    "msg" => "No se pudo crear la carpeta de imágenes",
                    "ruta_intentada" => $carpetaFisica
                ]);
            }
            error_log("Carpeta creada exitosamente");
        }

        // Verificar permisos de escritura
        if (!is_writable($carpetaFisica)) {
            error_log("ERROR: La carpeta no tiene permisos de escritura");
            return json_encode([
                "status" => "error",
                "msg" => "La carpeta no tiene permisos de escritura. Ejecuta: chmod 777 " . $carpetaFisica
            ]);
        }

        // Generar nombre único
        $nombreUnico = 'prod_' . uniqid() . '_' . time() . '.' . $extension;
        
        // Ruta física completa (donde se guarda)
        $rutaFisicaCompleta = $carpetaFisica . $nombreUnico;
        
        // Ruta que se guarda en BD
        $rutaBD = $carpetaRelativa . $nombreUnico;

        error_log("Nombre único: " . $nombreUnico);
        error_log("Ruta física completa: " . $rutaFisicaCompleta);
        error_log("Ruta BD: " . $rutaBD);
        error_log("Archivo temporal: " . $archivo['tmp_name']);

        // Intentar mover archivo
        if (!move_uploaded_file($archivo['tmp_name'], $rutaFisicaCompleta)) {
            $error = error_get_last();
            error_log("ERROR al mover archivo: " . print_r($error, true));
            
            return json_encode([
                "status" => "error",
                "msg" => "No se pudo guardar el archivo físicamente",
                "detalle" => $error['message'] ?? 'Error desconocido',
                "carpeta_fisica" => $carpetaFisica,
                "permisos" => substr(sprintf('%o', fileperms($carpetaFisica)), -4),
                "existe_carpeta" => is_dir($carpetaFisica) ? 'Sí' : 'No',
                "escribible" => is_writable($carpetaFisica) ? 'Sí' : 'No'
            ]);
        }

        error_log("✓ Archivo movido exitosamente a: " . $rutaFisicaCompleta);

        // Verificar que el archivo se guardó
        if (!file_exists($rutaFisicaCompleta)) {
            error_log("ERROR: El archivo no existe después de moverlo");
            return json_encode([
                "status" => "error",
                "msg" => "El archivo se movió pero no se encuentra en el destino"
            ]);
        }

        error_log("✓ Archivo verificado, existe en: " . $rutaFisicaCompleta);
        error_log("Tamaño del archivo guardado: " . filesize($rutaFisicaCompleta) . " bytes");

        // Obtener productos con ese SKU padre
        $productos = $this->imagenModel->obtenerProductosPorSkuPadre($sku_padre);
        
        if ($productos->rowCount() == 0) {
            // Eliminar imagen si no hay productos
            if (file_exists($rutaFisicaCompleta)) {
                unlink($rutaFisicaCompleta);
            }
            return json_encode([
                "status" => "error",
                "msg" => "No se encontraron productos con SKU padre: $sku_padre"
            ]);
        }

        error_log("Productos encontrados: " . $productos->rowCount());

        // Actualizar todos los productos
        $productosActualizados = 0;
        $errores = [];

        while ($producto = $productos->fetch()) {
            $id_producto = $producto['NUM_ID_PRODUCTO'];

            $datos_actualizacion = [
                [
                    "campo_nombre" => $campo_imagen,
                    "campo_marcador" => ":Imagen",
                    "campo_valor" => $rutaBD
                ]
            ];

            $condicion = [
                "condicion_campo" => "NUM_ID_PRODUCTO",
                "condicion_marcador" => ":ID",
                "condicion_valor" => $id_producto
            ];

            if ($this->productoModel->actualizarProductoModelo($datos_actualizacion, $condicion)) {
                $productosActualizados++;
                error_log("✓ Producto ID $id_producto actualizado");
            } else {
                $errores[] = "Error en producto ID: $id_producto";
                error_log("✗ Error al actualizar producto ID $id_producto");
            }
        }

        error_log("Productos actualizados: $productosActualizados");
        error_log("=== FIN CARGA DE IMAGEN ===");

        if ($productosActualizados > 0) {
            return json_encode([
                "status" => "ok",
                "msg" => "¡Imagen cargada exitosamente!",
                "detalles" => "Se actualizaron $productosActualizados producto(s) con SKU padre: $sku_padre",
                "campo" => $campo_imagen,
                "archivo" => $nombreUnico,
                "ruta_guardada" => $rutaBD,
                "ruta_fisica" => $rutaFisicaCompleta,
                "tamanio" => filesize($rutaFisicaCompleta) . " bytes"
            ]);
        } else {
            // Eliminar imagen si falló
            if (file_exists($rutaFisicaCompleta)) {
                unlink($rutaFisicaCompleta);
            }
            return json_encode([
                "status" => "error",
                "msg" => "No se pudo actualizar ningún producto en la base de datos",
                "errores" => $errores
            ]);
        }
    }

    /*---------- Listar imágenes ----------*/
    public function listarImagenesControlador()
    {
        $sku_padre = $this->limpiarCadena($_GET['sku'] ?? '');

        if (empty($sku_padre)) {
            return json_encode([
                "status" => "error",
                "msg" => "SKU padre no especificado"
            ]);
        }

        $producto = $this->imagenModel->buscarProductoPorSkuPadre($sku_padre);

        if ($producto->rowCount() == 0) {
            return json_encode([
                "status" => "info",
                "msg" => "No se encontró producto",
                "imagenes" => []
            ]);
        }

        $prod_data = $producto->fetch();
        $imagenes = [];

        $campos_imagen = [
            'VCH_IMAGEN_PRINCIPAL' => 'Imagen Principal',
            'VCH_IMAGEN2' => 'Imagen 2',
            'VCH_IMAGEN3' => 'Imagen 3',
            'VCH_IMAGEN4' => 'Imagen 4',
            'VCH_IMAGEN5' => 'Imagen 5',
            'VCH_IMAGEN6' => 'Imagen 6',
            'VCH_IMAGEN7' => 'Imagen 7',
            'VCH_IMAGEN8' => 'Imagen 8'
        ];

        foreach ($campos_imagen as $campo => $nombre) {
            if (!empty($prod_data[$campo])) {
                $imagenes[] = [
                    "campo" => $campo,
                    "nombre" => $nombre,
                    "ruta" => $prod_data[$campo],
                    "archivo" => basename($prod_data[$campo])
                ];
            }
        }

        return json_encode([
            "status" => "ok",
            "imagenes" => $imagenes,
            "total" => count($imagenes)
        ]);
    }

    /*---------- Eliminar imagen ----------*/
    public function eliminarImagenControlador()
    {
        $campo = $this->limpiarCadena($_POST['campo'] ?? '');
        $sku_padre = $this->limpiarCadena($_POST['sku_padre'] ?? '');

        if (empty($campo) || empty($sku_padre)) {
            return json_encode([
                "status" => "error",
                "msg" => "Datos incompletos"
            ]);
        }

        $productos = $this->imagenModel->obtenerProductosPorSkuPadre($sku_padre);

        if ($productos->rowCount() == 0) {
            return json_encode([
                "status" => "error",
                "msg" => "No se encontraron productos"
            ]);
        }

        // Obtener ruta de imagen
        $primer_producto = $productos->fetch();
        $ruta_imagen = $primer_producto[$campo];

        // Limpiar campo en todos los productos
        $productosActualizados = 0;
        $productos = $this->imagenModel->obtenerProductosPorSkuPadre($sku_padre);

        while ($producto = $productos->fetch()) {
            $datos_actualizacion = [
                [
                    "campo_nombre" => $campo,
                    "campo_marcador" => ":Imagen",
                    "campo_valor" => null
                ]
            ];

            $condicion = [
                "condicion_campo" => "NUM_ID_PRODUCTO",
                "condicion_marcador" => ":ID",
                "condicion_valor" => $producto['NUM_ID_PRODUCTO']
            ];

            if ($this->productoModel->actualizarProductoModelo($datos_actualizacion, $condicion)) {
                $productosActualizados++;
            }
        }

        // Eliminar archivo físico
        if (!empty($ruta_imagen)) {
            $raizProyecto = dirname(dirname(__DIR__));
            $rutaFisica = $raizProyecto . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $ruta_imagen);
            
            if (file_exists($rutaFisica)) {
                unlink($rutaFisica);
            }
        }

        if ($productosActualizados > 0) {
            return json_encode([
                "status" => "ok",
                "msg" => "Imagen eliminada de $productosActualizados producto(s)"
            ]);
        } else {
            return json_encode([
                "status" => "error",
                "msg" => "No se pudo eliminar"
            ]);
        }
    }

    /*---------- Buscar SKU padre ----------*/
    public function buscarSkuPadreControlador()
    {
        $sku_padre = $this->limpiarCadena($_POST['sku'] ?? '');

        if (empty($sku_padre)) {
            return json_encode([
                "status" => "error",
                "msg" => "SKU no especificado"
            ]);
        }

        $producto = $this->imagenModel->buscarProductoPorSkuPadre($sku_padre);

        if ($producto->rowCount() == 0) {
            return json_encode([
                "status" => "error",
                "msg" => "SKU padre '$sku_padre' no encontrado"
            ]);
        }

        $prod_data = $producto->fetch();
        $variantes = $this->imagenModel->obtenerProductosPorSkuPadre($sku_padre);
        $num_variantes = $variantes->rowCount();

        // Contar imágenes
        $campos = [
            'VCH_IMAGEN_PRINCIPAL', 'VCH_IMAGEN2', 'VCH_IMAGEN3', 'VCH_IMAGEN4',
            'VCH_IMAGEN5', 'VCH_IMAGEN6', 'VCH_IMAGEN7', 'VCH_IMAGEN8'
        ];

        $num_imagenes = 0;
        foreach ($campos as $campo) {
            if (!empty($prod_data[$campo])) {
                $num_imagenes++;
            }
        }

        return json_encode([
            "status" => "ok",
            "nombre" => $prod_data['VCH_NOMBRE'],
            "sku_padre" => $prod_data['VCH_SKU_PADRE'],
            "marca" => $prod_data['VCH_MARCA'] ?? 'Sin marca',
            "variantes" => $num_variantes,
            "imagenes" => $num_imagenes
        ]);
    }

    /*---------- Cargar Excel ----------*/
    public function cargarImagenesExcelControlador()
    {
        return json_encode([
            "status" => "info",
            "msg" => "Funcionalidad en desarrollo"
        ]);
    }
}