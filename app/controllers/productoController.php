<?php

namespace app\controllers;

use app\models\productoModel;
use app\models\claseModel;
use app\models\tiendaModel;

// Librerias para excel 
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class productoController extends mainController
{

    private $productoModel;
    private $claseModel;
    private $tiendaModel;

    public function __construct()
    {
        $this->productoModel = new productoModel();
        $this->claseModel = new claseModel();
        $this->tiendaModel = new tiendaModel();
    }

    /*---------- Registrar Producto ----------*/
    public function registrarProductoControlador()
    {
        $nextId = $this->productoModel->obtenerSiguienteIdProducto();
        $fechaActual = date('Y-m-d H:i:s');
        $usuario = $_SESSION['nombre_spm'] ?? 'SYSTEM';

        $producto_datos = [
            ["campo_nombre" => "NUM_ID_PRODUCTO", "campo_marcador" => ":ID", "campo_valor" => $nextId],
            ["campo_nombre" => "NUM_STOCK", "campo_marcador" => ":Stock", "campo_valor" => $this->limpiarCadena($_POST['NUM_STOCK'] ?? 0)],
            ["campo_nombre" => "NUM_ID_CLASE", "campo_marcador" => ":Clase", "campo_valor" => $this->limpiarCadena($_POST['NUM_ID_CLASE'])],
            ["campo_nombre" => "VCH_NOMBRE", "campo_marcador" => ":Nombre", "campo_valor" => $this->limpiarCadena($_POST['VCH_NOMBRE'])],
            ["campo_nombre" => "VCH_MARCA", "campo_marcador" => ":Marca", "campo_valor" => $this->limpiarCadena($_POST['VCH_MARCA'] ?? '')],
            ["campo_nombre" => "VCH_MODELO", "campo_marcador" => ":Modelo", "campo_valor" => $this->limpiarCadena($_POST['VCH_MODELO'] ?? '')],
            ["campo_nombre" => "VCH_DESCRIPCION", "campo_marcador" => ":Descripcion", "campo_valor" => $this->limpiarCadena($_POST['VCH_DESCRIPCION'] ?? '')],
            ["campo_nombre" => "VCH_CATEGORIA_PRIMARIA", "campo_marcador" => ":CategoriaPrimaria", "campo_valor" => $this->limpiarCadena($_POST['VCH_CATEGORIA_PRIMARIA'] ?? '')],
            ["campo_nombre" => "VCH_PAIS_PRODUCCION", "campo_marcador" => ":PaisProduccion", "campo_valor" => $this->limpiarCadena($_POST['VCH_PAIS_PRODUCCION'] ?? '')],
            ["campo_nombre" => "VCH_BASIC_COLOR", "campo_marcador" => ":BasicColor", "campo_valor" => $this->limpiarCadena($_POST['VCH_BASIC_COLOR'] ?? '')],
            ["campo_nombre" => "VCH_COLOR", "campo_marcador" => ":Color", "campo_valor" => $this->limpiarCadena($_POST['VCH_COLOR'] ?? '')],
            ["campo_nombre" => "VCH_SIZE", "campo_marcador" => ":Size", "campo_valor" => $this->limpiarCadena($_POST['VCH_SIZE'] ?? '')],
            ["campo_nombre" => "VCH_SKU_VENDEDOR", "campo_marcador" => ":SkuVendedor", "campo_valor" => $this->limpiarCadena($_POST['VCH_SKU_VENDEDOR'] ?? '')],
            ["campo_nombre" => "VCH_CODIGO_BARRAS", "campo_marcador" => ":CodigoBarras", "campo_valor" => $this->limpiarCadena($_POST['VCH_CODIGO_BARRAS'] ?? '')],
            ["campo_nombre" => "VCH_SKU_PADRE", "campo_marcador" => ":SkuPadre", "campo_valor" => $this->limpiarCadena($_POST['VCH_SKU_PADRE'] ?? '')],
            ["campo_nombre" => "NUM_QUANTITY_FALABELLA", "campo_marcador" => ":QuantityFalabella", "campo_valor" => $this->limpiarCadena($_POST['NUM_QUANTITY_FALABELLA'] ?? 0)],
            ["campo_nombre" => "NUM_PRICE_FALABELLA", "campo_marcador" => ":PriceFalabella", "campo_valor" => $this->limpiarCadena($_POST['NUM_PRICE_FALABELLA'])],
            ["campo_nombre" => "NUM_SALE_PRICE_FALABELLA", "campo_marcador" => ":SalePriceFalabella", "campo_valor" => $this->limpiarCadena($_POST['NUM_SALE_PRICE_FALABELLA'] ?? 0)],
            ["campo_nombre" => "FEC_SALE_START_DATE", "campo_marcador" => ":SaleStartDate", "campo_valor" => $this->limpiarCadena($_POST['FEC_SALE_START_DATE'] ?? null)],
            ["campo_nombre" => "FEC_SALE_END_DATE", "campo_marcador" => ":SaleEndDate", "campo_valor" => $this->limpiarCadena($_POST['FEC_SALE_END_DATE'] ?? null)],
            ["campo_nombre" => "VCH_FIT", "campo_marcador" => ":Fit", "campo_valor" => $this->limpiarCadena($_POST['VCH_FIT'] ?? '')],
            ["campo_nombre" => "VCH_COSTUME_GENRE", "campo_marcador" => ":CostumeGenre", "campo_valor" => $this->limpiarCadena($_POST['VCH_COSTUME_GENRE'] ?? '')],
            ["campo_nombre" => "VCH_PANTS_TYPE", "campo_marcador" => ":PantsType", "campo_valor" => $this->limpiarCadena($_POST['VCH_PANTS_TYPE'] ?? '')],
            ["campo_nombre" => "VCH_COMPOSITION", "campo_marcador" => ":Composition", "campo_valor" => $this->limpiarCadena($_POST['VCH_COMPOSITION'] ?? '')],
            ["campo_nombre" => "VCH_MATERIAL_VESTUARIO", "campo_marcador" => ":MaterialVestuario", "campo_valor" => $this->limpiarCadena($_POST['VCH_MATERIAL_VESTUARIO'] ?? '')],
            ["campo_nombre" => "VCH_CONDICION_PRODUCTO", "campo_marcador" => ":CondicionProducto", "campo_valor" => $this->limpiarCadena($_POST['VCH_CONDICION_PRODUCTO'] ?? '')],
            ["campo_nombre" => "VCH_GARANTIA_PRODUCTO", "campo_marcador" => ":GarantiaProducto", "campo_valor" => $this->limpiarCadena($_POST['VCH_GARANTIA_PRODUCTO'] ?? '')],
            ["campo_nombre" => "VCH_GARANTIA_VENDEDOR", "campo_marcador" => ":GarantiaVendedor", "campo_valor" => $this->limpiarCadena($_POST['VCH_GARANTIA_VENDEDOR'] ?? '')],
            ["campo_nombre" => "VCH_CONTENIDO_PAQUETE", "campo_marcador" => ":ContenidoPaquete", "campo_valor" => $this->limpiarCadena($_POST['VCH_CONTENIDO_PAQUETE'] ?? '')],
            ["campo_nombre" => "NUM_ANCHO_PAQUETE", "campo_marcador" => ":AnchoPaquete", "campo_valor" => $this->limpiarCadena($_POST['NUM_ANCHO_PAQUETE'] ?? null)],
            ["campo_nombre" => "NUM_LARGO_PAQUETE", "campo_marcador" => ":LargoPaquete", "campo_valor" => $this->limpiarCadena($_POST['NUM_LARGO_PAQUETE'] ?? null)],
            ["campo_nombre" => "NUM_ALTO_PAQUETE", "campo_marcador" => ":AltoPaquete", "campo_valor" => $this->limpiarCadena($_POST['NUM_ALTO_PAQUETE'] ?? null)],
            ["campo_nombre" => "NUM_PESO_PAQUETE", "campo_marcador" => ":PesoPaquete", "campo_valor" => $this->limpiarCadena($_POST['NUM_PESO_PAQUETE'] ?? null)],
            ["campo_nombre" => "VCH_IMAGEN_PRINCIPAL", "campo_marcador" => ":ImagenPrincipal", "campo_valor" => $this->limpiarCadena($_POST['VCH_IMAGEN_PRINCIPAL'] ?? '')],
            ["campo_nombre" => "VCH_IMAGEN2", "campo_marcador" => ":Imagen2", "campo_valor" => $this->limpiarCadena($_POST['VCH_IMAGEN2'] ?? '')],
            ["campo_nombre" => "VCH_IMAGEN3", "campo_marcador" => ":Imagen3", "campo_valor" => $this->limpiarCadena($_POST['VCH_IMAGEN3'] ?? '')],
            ["campo_nombre" => "VCH_IMAGEN4", "campo_marcador" => ":Imagen4", "campo_valor" => $this->limpiarCadena($_POST['VCH_IMAGEN4'] ?? '')],
            ["campo_nombre" => "VCH_IMAGEN5", "campo_marcador" => ":Imagen5", "campo_valor" => $this->limpiarCadena($_POST['VCH_IMAGEN5'] ?? '')],
            ["campo_nombre" => "VCH_IMAGEN6", "campo_marcador" => ":Imagen6", "campo_valor" => $this->limpiarCadena($_POST['VCH_IMAGEN6'] ?? '')],
            ["campo_nombre" => "VCH_IMAGEN7", "campo_marcador" => ":Imagen7", "campo_valor" => $this->limpiarCadena($_POST['VCH_IMAGEN7'] ?? '')],
            ["campo_nombre" => "VCH_IMAGEN8", "campo_marcador" => ":Imagen8", "campo_valor" => $this->limpiarCadena($_POST['VCH_IMAGEN8'] ?? '')],
            ["campo_nombre" => "VCH_MONEDA", "campo_marcador" => ":Moneda", "campo_valor" => $this->limpiarCadena($_POST['VCH_MONEDA'] ?? 'PEN')],
            ["campo_nombre" => "VCH_TIPO_PUBLI", "campo_marcador" => ":TipoPubli", "campo_valor" => $this->limpiarCadena($_POST['VCH_TIPO_PUBLI'] ?? '')],
            ["campo_nombre" => "VCH_FORM_ENV", "campo_marcador" => ":FormEnv", "campo_valor" => $this->limpiarCadena($_POST['VCH_FORM_ENV'] ?? '')],
            ["campo_nombre" => "VCH_COSTO_EN", "campo_marcador" => ":CostoEn", "campo_valor" => $this->limpiarCadena($_POST['VCH_COSTO_EN'] ?? '')],
            ["campo_nombre" => "VCH_RETIRO", "campo_marcador" => ":Retiro", "campo_valor" => $this->limpiarCadena($_POST['VCH_RETIRO'] ?? '')],
            ["campo_nombre" => "VCH_PESO_PROD", "campo_marcador" => ":PesoProd", "campo_valor" => $this->limpiarCadena($_POST['VCH_PESO_PROD'] ?? null)],
            ["campo_nombre" => "VCH_LONG_PROD", "campo_marcador" => ":LongProd", "campo_valor" => $this->limpiarCadena($_POST['VCH_LONG_PROD'] ?? null)],
            ["campo_nombre" => "VCH_ANCHO_PROD", "campo_marcador" => ":AnchoProd", "campo_valor" => $this->limpiarCadena($_POST['VCH_ANCHO_PROD'] ?? null)],
            ["campo_nombre" => "VCH_ALTURA_PROD", "campo_marcador" => ":AlturaProd", "campo_valor" => $this->limpiarCadena($_POST['VCH_ALTURA_PROD'] ?? null)],
            ["campo_nombre" => "VCH_TIPO_CUE", "campo_marcador" => ":TipoCue", "campo_valor" => $this->limpiarCadena($_POST['VCH_TIPO_CUE'] ?? '')],
            ["campo_nombre" => "VCH_TIPO_PUN", "campo_marcador" => ":TipoPun", "campo_valor" => $this->limpiarCadena($_POST['VCH_TIPO_PUN'] ?? '')],
            ["campo_nombre" => "VCH_TIPO_CIER", "campo_marcador" => ":TipoCier", "campo_valor" => $this->limpiarCadena($_POST['VCH_TIPO_CIER'] ?? '')],
            ["campo_nombre" => "VCH_TIPO_GARANT", "campo_marcador" => ":TipoGarant", "campo_valor" => $this->limpiarCadena($_POST['VCH_TIPO_GARANT'] ?? '')],
            ["campo_nombre" => "VCH_TABLA_TALLA", "campo_marcador" => ":TablaTalla", "campo_valor" => $this->limpiarCadena($_POST['VCH_TABLA_TALLA'] ?? '')],
            ["campo_nombre" => "VCH_TAMANIO_PROD", "campo_marcador" => ":TamanioProd", "campo_valor" => $this->limpiarCadena($_POST['VCH_TAMANIO_PROD'] ?? '')],
            ["campo_nombre" => "FEC_FECHA_CREACION", "campo_marcador" => ":FechaCreacion", "campo_valor" => $fechaActual],
            ["campo_nombre" => "VCH_USER_CREACION", "campo_marcador" => ":UserCreacion", "campo_valor" => $usuario]
        ];

        if (empty($_POST['VCH_NOMBRE']) || empty($_POST['NUM_PRICE_FALABELLA'])) {
            return json_encode([
                "status" => "error",
                "msg" => "Nombre y Precio son obligatorios"
            ]);
        }

        $registrar = $this->productoModel->registrarProductoModelo($producto_datos);

        if ($registrar->rowCount() == 1) {
            return json_encode([
                "status" => "ok",
                "msg" => "Producto registrado correctamente",
                "id" => $nextId
            ]);
        } else {
            return json_encode([
                "status" => "error",
                "msg" => "No se pudo registrar el producto"
            ]);
        }
    }


    /*---------- Actualizar Producto ----------*/

    // Agregar este método en tu productoController.php


    public function actualizarProductoControlador()
    {
        try {
            $id_producto = isset($_POST['id_producto']) ? (int)$_POST['id_producto'] : 0;

            if ($id_producto <= 0) {
                return json_encode(['status' => 'error', 'msg' => 'ID inválido']);
            }

            // Usar el productoModel ya disponible en la clase
            $conn = $this->productoModel->conectar();

            // Obtener columnas de la tabla (nombre correcto: producto, no productos)
            $stmt = $conn->query("DESCRIBE producto");
            $columnasTabla = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            // Construir SET dinámico excluyendo el ID
            $campos = [];
            $parametros = [];

            foreach ($_POST as $campo => $valor) {
                // Excluir campos de control
                if ($campo === 'modulo_producto' || $campo === 'id_producto') {
                    continue;
                }

                // Verificar que el campo existe en la tabla
                if (in_array($campo, $columnasTabla)) {
                    $campos[] = "$campo = ?";
                    $parametros[] = $valor;
                }
            }

            if (empty($campos)) {
                return json_encode(['status' => 'error', 'msg' => 'No hay campos para actualizar']);
            }

            // Agregar el ID al final para el WHERE
            $parametros[] = $id_producto;

            // Construir y ejecutar query (nombre correcto: producto)
            $sql = "UPDATE producto SET " . implode(', ', $campos) . " WHERE NUM_ID_PRODUCTO = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute($parametros);

            return json_encode([
                'status' => 'ok',
                'msg' => 'Producto actualizado correctamente',
                'campos_actualizados' => count($campos)
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'status' => 'error',
                'msg' => 'Error al actualizar: ' . $e->getMessage()
            ]);
        }
    }
    /*---------- Eliminar Producto ----------*/
    public function eliminarProductoControlador()
    {
        $id = $this->limpiarCadena($_POST['id']);

        $datos = $this->productoModel->buscarProductoPorIdModelo($id);
        if ($datos->rowCount() <= 0) {
            return json_encode([
                "status" => "error",
                "msg" => "Producto no encontrado"
            ]);
        }

        $eliminar = $this->productoModel->eliminarProductoModelo($id);

        if ($eliminar->rowCount() == 1) {
            return json_encode([
                "status" => "ok",
                "msg" => "Producto eliminado correctamente"
            ]);
        } else {
            return json_encode([
                "status" => "error",
                "msg" => "No se pudo eliminar el producto"
            ]);
        }
    }

   

    /*---------- Listar Productos ----------*/
    public function listarProductosControlador($idClase = 0, $idTienda = 0, $busqueda = '')
    {
      

        if ($idClase == 0 && empty($busqueda)) {
           //listar todos
            return $this->productoModel->listarTodosProductosModelo();
        } else {
            
            return $this->productoModel->listarProductosFiltrosModelo($idClase, $busqueda);
        }
    }

    
    /*---------- Obtener campos de plantilla con opciones de lista ----------*/
    public function obtenerCamposPlantillaControlador()
    {
        try {
            $idClase = isset($_POST['id_clase']) ? intval($_POST['id_clase']) : 0;
            $idTienda = isset($_POST['id_tienda']) ? intval($_POST['id_tienda']) : 0;

            error_log("🔍 Recibido - Clase: $idClase, Tienda: $idTienda");

            if ($idClase == 0) {
                return json_encode([
                    'status' => 'error',
                    'msg' => 'Debe seleccionar una clase'
                ], JSON_UNESCAPED_UNICODE);
            }

            $campos = $this->productoModel->obtenerCamposPlantillaModelo($idClase, $idTienda);

            if ($campos->rowCount() == 0) {
                return json_encode([
                    'status' => 'error',
                    'msg' => 'No existe plantilla configurada para esta clase/tienda'
                ], JSON_UNESCAPED_UNICODE);
            }

            $resultado = [];
            $camposVistos = [];

            while ($campo = $campos->fetch()) {
                // Verificar duplicados
                if (in_array($campo['columna_producto'], $camposVistos)) {
                    continue;
                }
                $camposVistos[] = $campo['columna_producto'];

                $campoDatos = [
                    'encabezado' => $campo['encabezado'],
                    'columna' => $campo['columna_producto'],
                    'obligatorio' => $campo['obligatorio'],
                    'tipo_campo' => $this->determinarTipoCampo($campo['columna_producto']),
                    'orden' => $campo['orden'],
                    'juego' => $campo['juego_lista'] ?? null,
                    'opciones' => []
                ];

                // Si tiene VCH_JUEGO, cargar opciones
                if (!empty($campo['juego_lista'])) {
                    $opciones = $this->productoModel->obtenerOpcionesListaPorJuegoModelo($campo['juego_lista'], $idTienda);

                    while ($opcion = $opciones->fetch()) {
           
                        $campoDatos['opciones'][] = [
                            'codigo' => $opcion['VCH_CODIGO'],
                            'descripcion' => $opcion['VCH_CODIGO'] 
                        ];
                    }

                    if (count($campoDatos['opciones']) > 0) {
                        $campoDatos['tipo_campo'] = 'select';
                    }
                }

                $resultado[] = $campoDatos;
            }

            return json_encode([
                'status' => 'ok',
                'campos' => $resultado
            ], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            error_log("❌ Error en obtenerCamposPlantillaControlador: " . $e->getMessage());
            return json_encode([
                'status' => 'error',
                'msg' => 'Error al obtener campos: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    /*---------- Determinar tipo de campo HTML según nombre de columna ----------*/
    private function determinarTipoCampo($nombreColumna)
    {
        if (strpos($nombreColumna, 'NUM_') === 0) {
            if (strpos($nombreColumna, 'PRICE') !== false || strpos($nombreColumna, 'SALE') !== false) {
                return 'number'; 
            }
            return 'number'; 
        }

        if (strpos($nombreColumna, 'FEC_') === 0) {
            return 'datetime-local';
        }

        if (strpos($nombreColumna, 'VCH_DESCRIPCION') !== false) {
            return 'textarea';
        }

        if (strpos($nombreColumna, 'VCH_IMAGEN') !== false) {
            return 'url';
        }

        return 'text';
    }

    /*------- Obtener Plantilla Vacía en Excel ----------*/
    public function obtenerPlantillaVaciaControlador()
    {
        try {
            // Obtener todas las columnas de la tabla producto
            $columnas = $this->productoModel->obtenerColumnasTablaModelo();

            $camposExcluidos = [
                'NUM_ID_PRODUCTO',
                'FEC_FECHA_CREACION',
                'VCH_USER_CREACION',
                'FEC_FECHA_MODIFICACION',
                'VCH_USER_MODIFICACION'
            ];

            $camposObligatorios = [
                'NUM_ID_CLASE',
                'VCH_NOMBRE',
                'NUM_PRICE_FALABELLA'
            ];

            $headers = [];
            while ($col = $columnas->fetch()) {
                $nombreCampo = $col['Field'];

                if (!in_array($nombreCampo, $camposExcluidos)) {
                    $esObligatorio = in_array($nombreCampo, $camposObligatorios);
                    $headers[] = [
                        'campo' => $nombreCampo,
                        'obligatorio' => $esObligatorio
                    ];
                }
            }

            if (empty($headers)) {
                die("Error: No se pudieron obtener las columnas");
            }

            // Crear archivo Excel
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Plantilla Productos');

            // Escribir encabezados
            $col = 1;
            foreach ($headers as $header) {
                $cellCoord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . '1';
                $nombreHeader = $header['campo'] . ($header['obligatorio'] ? ' *' : '');
                $sheet->setCellValue($cellCoord, $nombreHeader);

                // Estilo del encabezado
                $sheet->getStyle($cellCoord)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                        'size' => 11
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $header['obligatorio'] ? 'E74C3C' : '3498DB']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ]
                    ]
                ]);

                // Ajustar ancho de columna
                $sheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col))
                    ->setWidth(20);

                $col++;
            }

            // Ajustar altura de fila de encabezados
            $sheet->getRowDimension(1)->setRowHeight(25);

            // Agregar comentario en la primera celda
            $sheet->getComment('A1')->getText()->createTextRun(
                "Plantilla Vacía - Complete los datos de productos\n\n" .
                    "Campos obligatorios (*): NUM_ID_CLASE, VCH_NOMBRE, NUM_PRICE_FALABELLA\n\n" .
                    "Nota: NUM_ID_PRODUCTO se genera automáticamente."
            );
            $sheet->getComment('A1')->setWidth('400px');
            $sheet->getComment('A1')->setHeight('150px');

            // Generar archivo
            $filename = 'plantilla_vacia_productos_' . date('Ymd_His') . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            error_log("Error en obtenerPlantillaVaciaControlador: " . $e->getMessage());
            die("Error al generar plantilla: " . $e->getMessage());
        }
    }

    




    public function obtenerPlantillaExcelControlador()
    {
        try {
            $idClase = isset($_GET['id_clase']) ? intval($_GET['id_clase']) : (isset($_POST['id_clase']) ? intval($_POST['id_clase']) : 0);
            $idTienda = isset($_GET['id_tienda']) ? intval($_GET['id_tienda']) : (isset($_POST['id_tienda']) ? intval($_POST['id_tienda']) : 0);

            error_log(" Filtros recibidos - Clase: $idClase, Tienda: $idTienda");

            if ($idClase == 0) {
                die("Error: Debe seleccionar una Clase");
            }

            // Obtener el NUM_ID_PLANTILLA específico según Clase + Tienda
            $idPlantilla = $this->productoModel->obtenerIdPlantillaModelo($idClase, $idTienda);

            if (!$idPlantilla) {
                die("Error: No existe plantilla configurada para Clase: $idClase" . ($idTienda > 0 ? ", Tienda: $idTienda" : ""));
            }

            error_log(" Plantilla encontrada: ID = $idPlantilla");

            //  Obtener columnas dinámicas desde plant_detalle ORDENADAS por NUM_ORDEN
            $columnasResult = $this->productoModel->obtenerColumnasPorPlantillaModelo($idPlantilla);

            $headers = [];
            $camposBD = [];

            while ($col = $columnasResult->fetch()) {
                $etiqueta = $col['CAMPO_ETIQUETA'];
                $nombreColumnaProducto = trim($col['CAMPO_BD']);

                if (!empty($nombreColumnaProducto)) {
                    $headers[] = [
                        'nombre' => $etiqueta,
                        'obligatorio' => ($col['OBLIGATORIO'] == 1)
                    ];
                    $camposBD[] = $nombreColumnaProducto;
                }
            }

            if (empty($headers) || empty($camposBD)) {
                die("Error: No se encontraron columnas configuradas en la plantilla ID: $idPlantilla");
            }

            error_log(" Columnas a consultar (" . count($camposBD) . "): " . implode(", ", $camposBD));

            //  Construir SELECT dinámico
            $columnasStr = 'p.' . implode(', p.', $camposBD);

            $sql = "SELECT $columnasStr 
            FROM producto p
            WHERE p.NUM_ID_CLASE = :IdClase";

            $sql .= " ORDER BY p.NUM_ID_PRODUCTO";

            error_log(" SQL generado: " . $sql);

            //  Ejecutar consulta
            $stmt = $this->productoModel->conectar()->prepare($sql);
            $stmt->bindParam(":IdClase", $idClase, \PDO::PARAM_INT);
            $stmt->execute();

            $totalProductos = $stmt->rowCount();
            error_log(" Productos encontrados: $totalProductos");

            if ($totalProductos == 0) {
                error_log(" No hay productos para Clase: $idClase");
            }

            // CARGAR CACHE DE LISTAS PARA TRADUCIR CÓDIGOS
            $cacheListas = [];
            if ($idTienda > 0) {
                try {
                    $sqlListas = "SELECT VCH_CODIGO, VCH_DESCRIPCION FROM listas WHERE NUM_ID_TIENDA = :tienda AND VCH_ESTADO = 1";
                    $stmtListas = $this->productoModel->conectar()->prepare($sqlListas);
                    $stmtListas->bindParam(":tienda", $idTienda, \PDO::PARAM_INT);
                    $stmtListas->execute();

                    while ($lista = $stmtListas->fetch()) {
                        $cacheListas[$lista['VCH_CODIGO']] = $lista['VCH_DESCRIPCION'];
                    }

                    error_log(" Cache de listas cargado: " . count($cacheListas) . " códigos");
                } catch (\Exception $e) {
                    error_log("Error al cargar cache de listas: " . $e->getMessage());
                }
            }

            // PASO 5: Crear archivo Excel
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Obtener nombre de clase
            $nombreClase = "Clase_" . $idClase;
            $nombreTienda = "";

            try {
                $sqlClase = "SELECT VCH_NOMBRE FROM clase WHERE NUM_ID_CLASE = :ID LIMIT 1";
                $stmtClase = $this->productoModel->conectar()->prepare($sqlClase);
                $stmtClase->bindParam(":ID", $idClase, \PDO::PARAM_INT);
                $stmtClase->execute();

                if ($stmtClase->rowCount() > 0) {
                    $clase = $stmtClase->fetch();
                    $nombreClase = $clase['VCH_NOMBRE'];
                }
            } catch (\Exception $e) {
                error_log("Error al obtener nombre de clase: " . $e->getMessage());
            }

            if ($idTienda > 0) {
                try {
                    $sqlTienda = "SELECT VCH_TIENDA FROM tienda WHERE NUM_ID_TIENDA = :ID LIMIT 1";
                    $stmtTienda = $this->productoModel->conectar()->prepare($sqlTienda);
                    $stmtTienda->bindParam(":ID", $idTienda, \PDO::PARAM_INT);
                    $stmtTienda->execute();

                    if ($stmtTienda->rowCount() > 0) {
                        $tienda = $stmtTienda->fetch();
                        $nombreTienda = " - " . $tienda['VCH_TIENDA'];
                    }
                } catch (\Exception $e) {
                    error_log("Error al obtener nombre de tienda: " . $e->getMessage());
                }
            }

            $tituloHoja = $nombreClase . $nombreTienda;
            $sheet->setTitle(substr($tituloHoja, 0, 31));

            //   Escribir encabezados
            $col = 1;
            foreach ($headers as $header) {
                $cellCoord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . '1';
                $nombreHeader = $header['nombre'] . ($header['obligatorio'] ? ' *' : '');
                $sheet->setCellValue($cellCoord, $nombreHeader);

                $sheet->getStyle($cellCoord)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                        'size' => 11
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $header['obligatorio'] ? 'E74C3C' : '3498DB']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ]
                    ]
                ]);

                $sheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col))
                    ->setWidth(20);

                $col++;
            }

            $sheet->getRowDimension(1)->setRowHeight(25);

            //  Escribir datos de productos
            $fila = 2;
            while ($producto = $stmt->fetch()) {
                $col = 1;

                foreach ($camposBD as $campo) {
                    $valor = $producto[$campo] ?? '';

                    //  TRADUCIR CÓDIGOS A DESCRIPCIONES
                    if (!empty($valor) && is_string($valor) && isset($cacheListas[$valor])) {
                        $valor = $cacheListas[$valor];
                    }

                    // Formatear valores especiales
                    if (strpos($campo, 'FEC_') === 0 && !empty($valor) && $valor != '1900-01-01 00:00:00') {
                        try {
                            $fecha = new \DateTime($valor);
                            $valor = $fecha->format('d/m/Y H:i:s');
                        } catch (\Exception $e) {
                            $valor = '';
                        }
                    } elseif (strpos($campo, 'NUM_PRICE') !== false || strpos($campo, 'NUM_SALE') !== false) {
                        $valor = $valor > 0 ? floatval($valor) : 0;
                    } elseif (is_numeric($valor) && strpos($campo, 'NUM_') === 0) {
                        $valor = floatval($valor);
                    }

                    $cellCoord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $fila;
                    $sheet->setCellValue($cellCoord, $valor);

                    $sheet->getStyle($cellCoord)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'CCCCCC']
                            ]
                        ]
                    ]);

                    $col++;
                }

                $fila++;
            }

            // Aplicar filtros automáticos
            $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
            $sheet->setAutoFilter('A1:' . $lastCol . '1');

            // Inmovilizar primera fila
            $sheet->freezePane('A2');

            // Agregar comentario informativo
            $comentario = "Plantilla: $tituloHoja\n";
            $comentario .= "ID Plantilla: $idPlantilla\n";
            $comentario .= "Columnas: " . count($headers) . "\n";
            $comentario .= "Productos: " . ($fila - 2) . "\n\n";
            $comentario .= "Los campos con (*) son obligatorios.";

            $sheet->getComment('A1')->getText()->createTextRun($comentario);
            $sheet->getComment('A1')->setWidth('400px');
            $sheet->getComment('A1')->setHeight('140px');

            // Generar archivo
            $filename = 'plantilla_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $nombreClase) .
                ($nombreTienda ? '_' . preg_replace('/[^A-Za-z0-9_-]/', '_', trim($nombreTienda, ' -')) : '') .
                '_' . date('Ymd_His') . '.xlsx';

            error_log("Generando archivo: $filename");

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            error_log("❌ Error crítico en obtenerPlantillaExcelControlador: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            die("Error al generar plantilla: " . $e->getMessage());
        }
    }





    /*----------  IMPORTAR CSV/EXCEL ---------*/
    public function importarCSVControlador()
    {
        // Limpiar cualquier output previo
        ob_clean();

        try {
            if (!isset($_FILES['archivo_csv']) || $_FILES['archivo_csv']['error'] !== UPLOAD_ERR_OK) {
                return json_encode([
                    "status" => "error",
                    "msg" => "Error al subir el archivo. Código: " . ($_FILES['archivo_csv']['error'] ?? 'desconocido')
                ]);
            }

            $archivo = $_FILES['archivo_csv']['tmp_name'];
            $nombreArchivo = $_FILES['archivo_csv']['name'];
            $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));

            // Log
            error_log("Archivo recibido: $nombreArchivo (Extensión: $extension)");

            //  Si es Excel, convertir a CSV primero
            $archivoTemporal = null;
            if (in_array($extension, ['xlsx', 'xls'])) {
                error_log("Detectado archivo Excel, se pasa a formato  CSV");

                try {
                    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);
                    $sheet = $spreadsheet->getActiveSheet();

                    // Crear archivo temporal CSV
                    $archivoTemporal = tempnam(sys_get_temp_dir(), 'excel_') . '.csv';
                    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
                    $writer->setDelimiter(',');
                    $writer->setEnclosure('"');
                    $writer->setLineEnding("\n");
                    $writer->setSheetIndex(0);
                    $writer->save($archivoTemporal);

                    $archivo = $archivoTemporal;
                    error_log(" Conversión exitosa: $archivoTemporal");
                } catch (\Exception $e) {
                    error_log(" Error convirtiendo Excel: " . $e->getMessage());
                    return json_encode([
                        "status" => "error",
                        "msg" => "Error al procesar Excel: " . $e->getMessage()
                    ]);
                }
            }

            $productos = [];
            $errores = [];
            $fechaActual = date('Y-m-d H:i:s');
            $usuario = $_SESSION['nombre_spm'] ?? 'SYSTEM';

            // Leer archivo
            $contenido = file_get_contents($archivo);

            if ($contenido === false) {
                return json_encode([
                    "status" => "error",
                    "msg" => "No se pudo leer el contenido del archivo"
                ]);
            }

            // Detectar y convertir encoding
            if (!mb_check_encoding($contenido, 'UTF-8')) {
                $contenido = mb_convert_encoding($contenido, 'UTF-8', 'ISO-8859-1,Windows-1252');
            }

            // Normalizar finales de línea
            $contenido = str_replace(["\r\n", "\r"], "\n", $contenido);
            $lineas = explode("\n", $contenido);

            error_log("📊 Total líneas en archivo: " . count($lineas));

            // Detectar delimitador
            $primeraLineaConDatos = '';
            foreach ($lineas as $linea) {
                if (!empty(trim($linea))) {
                    $primeraLineaConDatos = $linea;
                    break;
                }
            }

            $delimitador = ',';
            $cantidadTabs = substr_count($primeraLineaConDatos, "\t");
            $cantidadComas = substr_count($primeraLineaConDatos, ',');

            if ($cantidadTabs > $cantidadComas && $cantidadTabs > 3) {
                $delimitador = "\t";
            }

            error_log(" Delimitador detectado: " . ($delimitador == "\t" ? "TAB" : "COMA"));

            // Obtener headers
            $headers = null;
            $lineaInicioHeaders = 0;

            foreach ($lineas as $index => $linea) {
                $lineaTrim = trim($linea);
                if (!empty($lineaTrim)) {
                    $headers = str_getcsv($lineaTrim, $delimitador);
                    $lineaInicioHeaders = $index;
                    break;
                }
            }

            if (!$headers || empty($headers)) {
                return json_encode([
                    "status" => "error",
                    "msg" => "No se encontraron encabezados en el archivo"
                ]);
            }

            // Limpiar headers
            $headers = array_map(function ($h) {
                $h = str_replace("\xEF\xBB\xBF", '', $h);
                return trim(str_replace('*', '', $h));
            }, $headers);

            error_log(" Headers: " . implode(" | ", array_slice($headers, 0, 5)) . "...");

            $totalFilasProcesadas = 0;
            $idInicial = $this->productoModel->obtenerSiguienteIdProducto();

            // Procesar cada línea
            for ($i = $lineaInicioHeaders + 1; $i < count($lineas); $i++) {
                $linea = trim($lineas[$i]);

                if (empty($linea)) {
                    continue;
                }

                $data = str_getcsv($linea, $delimitador);
                $data = array_map('trim', $data);

                // Ajustar columnas
                if (count($data) < count($headers)) {
                    while (count($data) < count($headers)) {
                        $data[] = '';
                    }
                } elseif (count($data) > count($headers)) {
                    $data = array_slice($data, 0, count($headers));
                }

                $nextId = $idInicial + $totalFilasProcesadas;

                $producto = [];
                $producto[] = ["campo_nombre" => "NUM_ID_PRODUCTO", "campo_marcador" => ":ID", "campo_valor" => $nextId];

                $tieneClase = false;
                $tieneNombre = false;
                $tienePrecio = false;
                $nombreProducto = '';

                foreach ($headers as $index => $header) {
                    $valor = isset($data[$index]) ? trim($data[$index]) : '';
                    $campoNombre = $this->mapearEncabezadoACampo($header);

                    if ($campoNombre && $campoNombre != 'NUM_ID_PRODUCTO') {
                        $valorFinal = $this->procesarValorCampo($campoNombre, $valor);

                        $producto[] = [
                            "campo_nombre" => $campoNombre,
                            "campo_marcador" => ":$campoNombre",
                            "campo_valor" => $valorFinal
                        ];

                        if ($campoNombre == 'NUM_ID_CLASE' && !empty($valor)) {
                            $tieneClase = true;
                        }
                        if ($campoNombre == 'VCH_NOMBRE' && !empty($valor)) {
                            $tieneNombre = true;
                            $nombreProducto = $valor;
                        }
                        if ($campoNombre == 'NUM_PRICE_FALABELLA' && !empty($valor)) {
                            $tienePrecio = true;
                        }
                    }
                }

                // Validar campos obligatorios
                if (!$tieneClase || !$tieneNombre || !$tienePrecio) {
                    $camposFaltantes = [];
                    if (!$tieneClase) $camposFaltantes[] = 'Clase';
                    if (!$tieneNombre) $camposFaltantes[] = 'Nombre';
                    if (!$tienePrecio) $camposFaltantes[] = 'Precio';

                    $errores[] = "Línea " . ($i + 1) . ": Faltan " . implode(', ', $camposFaltantes);
                    continue;
                }

                $producto[] = ["campo_nombre" => "FEC_FECHA_CREACION", "campo_marcador" => ":FechaCreacion", "campo_valor" => $fechaActual];
                $producto[] = ["campo_nombre" => "VCH_USER_CREACION", "campo_marcador" => ":UserCreacion", "campo_valor" => $usuario];

                $productos[] = $producto;
                $totalFilasProcesadas++;
            }

            if (empty($productos)) {
                return json_encode([
                    "status" => "error",
                    "msg" => "No se encontraron productos válidos para importar",
                    "errores" => $errores
                ]);
            }

            // Insertar en BD
            $resultado = $this->productoModel->insertarProductosCSVModelo($productos);

            // Limpiar archivo temporal si existe
            if ($archivoTemporal && file_exists($archivoTemporal)) {
                unlink($archivoTemporal);
            }

            return json_encode([
                "status" => "ok",
                "msg" => "Importación completada exitosamente",
                "insertados" => $resultado['insertados'],
                "total_lineas" => $totalFilasProcesadas,
                "errores" => array_merge($errores, $resultado['errores'])
            ]);
        } catch (\Exception $e) {
            error_log(" Error crítico en importación: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());

            return json_encode([
                "status" => "error",
                "msg" => "Error al procesar el archivo: " . $e->getMessage()
            ]);
        }
    }




    /*---------- Procesar valor según tipo de campo ----------*/
    private function procesarValorCampo($nombreCampo, $valor)
    {
        $valor = trim($valor);

        if (
            empty($valor) ||
            strtoupper($valor) === 'NULL' ||
            strtoupper($valor) === 'N/A' ||
            $valor === '-'
        ) {

            if (preg_match('/^NUM_(STOCK|ID_CLASE|QUANTITY)/', $nombreCampo)) {
                return 0;
            }

            if (preg_match('/(PRICE|ANCHO|LARGO|ALTO|PESO)/', $nombreCampo)) {
                return 0.00;
            }

            if ($nombreCampo === 'FEC_SALE_START_DATE') {
                return date('Y-m-d H:i:s');
            }

            if ($nombreCampo === 'FEC_SALE_END_DATE') {
                return date('Y-m-d H:i:s', strtotime('+90 days'));
            }

            if (preg_match('/^FEC_/', $nombreCampo)) {
                return '1900-01-01 00:00:00';
            }

            return '';
        }

        if (preg_match('/^FEC_/', $nombreCampo) && !empty($valor)) {
            if (strtoupper($valor) === 'AUTO' || strtoupper($valor) === 'AUTOMATICO') {
                if ($nombreCampo === 'FEC_SALE_START_DATE') {
                    return date('Y-m-d H:i:s');
                }
                if ($nombreCampo === 'FEC_SALE_END_DATE') {
                    return date('Y-m-d H:i:s', strtotime('+90 days'));
                }
            }

            if (preg_match('#^(\d{1,2})/(\d{1,2})/(\d{4})#', $valor, $matches)) {
                $dia = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                $mes = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
                $anio = $matches[3];

                $hora = '00:00:00';
                if (preg_match('/(\d{1,2}:\d{2}(?::\d{2})?)/', $valor, $horaMatch)) {
                    $hora = $horaMatch[1];
                    if (substr_count($hora, ':') == 1) {
                        $hora .= ':00';
                    }
                }

                $valor = "$anio-$mes-$dia $hora";
            } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $valor)) {
                $valor .= ' 00:00:00';
            } elseif (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $valor)) {
                $valor .= ':00';
            }
        }

        if (preg_match('/(PRICE|ANCHO|LARGO|ALTO|PESO)/', $nombreCampo)) {
            $valor = str_replace(',', '.', $valor);
            return number_format((float)$valor, 2, '.', '');
        }

        return $valor;
    }

    /*---------- Mapear encabezado CSV a campo BD ----------*/
    private function mapearEncabezadoACampo($encabezado)
    {
        $camposBD = [
            'NUM_STOCK',
            'NUM_ID_CLASE',
            'VCH_NOMBRE',
            'VCH_MARCA',
            'VCH_MODELO',
            'VCH_DESCRIPCION',
            'VCH_CATEGORIA_PRIMARIA',
            'VCH_PAIS_PRODUCCION',
            'VCH_BASIC_COLOR',
            'VCH_COLOR',
            'VCH_SIZE',
            'VCH_SKU_VENDEDOR',
            'VCH_CODIGO_BARRAS',
            'VCH_SKU_PADRE',
            'NUM_QUANTITY_FALABELLA',
            'NUM_PRICE_FALABELLA',
            'NUM_SALE_PRICE_FALABELLA',
            'FEC_SALE_START_DATE',
            'FEC_SALE_END_DATE',
            'VCH_FIT',
            'VCH_COSTUME_GENRE',
            'VCH_PANTS_TYPE',
            'VCH_COMPOSITION',
            'VCH_MATERIAL_VESTUARIO',
            'VCH_CONDICION_PRODUCTO',
            'VCH_GARANTIA_PRODUCTO',
            'VCH_GARANTIA_VENDEDOR',
            'VCH_CONTENIDO_PAQUETE',
            'NUM_ANCHO_PAQUETE',
            'NUM_LARGO_PAQUETE',
            'NUM_ALTO_PAQUETE',
            'NUM_PESO_PAQUETE',
            'VCH_IMAGEN_PRINCIPAL',
            'VCH_IMAGEN2',
            'VCH_IMAGEN3',
            'VCH_IMAGEN4',
            'VCH_IMAGEN5',
            'VCH_IMAGEN6',
            'VCH_IMAGEN7',
            'VCH_IMAGEN8',
            'VCH_MONEDA',
            'VCH_TIPO_PUBLI',
            'VCH_FORM_ENV',
            'VCH_COSTO_EN',
            'VCH_RETIRO',
            'VCH_PESO_PROD',
            'VCH_LONG_PROD',
            'VCH_ANCHO_PROD',
            'VCH_ALTURA_PROD',
            'VCH_TIPO_CUE',
            'VCH_TIPO_PUN',
            'VCH_TIPO_CIER',
            'VCH_TIPO_GARANT',
            'VCH_TABLA_TALLA',
            'VCH_TAMANIO_PROD'
        ];

        $encabezadoUpper = strtoupper(trim($encabezado));

        if (in_array($encabezadoUpper, $camposBD)) {
            return $encabezadoUpper;
        }

        $mapeo = [
            'STOCK' => 'NUM_STOCK',
            'ID CLASE' => 'NUM_ID_CLASE',
            'CLASE' => 'NUM_ID_CLASE',
            'NOMBRE' => 'VCH_NOMBRE',
            'MARCA' => 'VCH_MARCA',
            'MODELO' => 'VCH_MODELO',
            'DESCRIPCION' => 'VCH_DESCRIPCION',
            'CATEGORIA PRIMARIA' => 'VCH_CATEGORIA_PRIMARIA',
            'PAIS PRODUCCION' => 'VCH_PAIS_PRODUCCION',
            'BASIC COLOR' => 'VCH_BASIC_COLOR',
            'COLOR' => 'VCH_COLOR',
            'SIZE' => 'VCH_SIZE',
            'TALLA' => 'VCH_SIZE',
            'SKU VENDEDOR' => 'VCH_SKU_VENDEDOR',
            'CODIGO BARRAS' => 'VCH_CODIGO_BARRAS',
            'SKU PADRE' => 'VCH_SKU_PADRE',
            'QUANTITY FALABELLA' => 'NUM_QUANTITY_FALABELLA',
            'PRICE FALABELLA' => 'NUM_PRICE_FALABELLA',
            'PRECIO' => 'NUM_PRICE_FALABELLA',
            'SALE PRICE FALABELLA' => 'NUM_SALE_PRICE_FALABELLA',
            'PRECIO OFERTA' => 'NUM_SALE_PRICE_FALABELLA',
            'SALE START DATE' => 'FEC_SALE_START_DATE',
            'SALE END DATE' => 'FEC_SALE_END_DATE',
            'FIT' => 'VCH_FIT',
            'COSTUME GENRE' => 'VCH_COSTUME_GENRE',
            'GENERO' => 'VCH_COSTUME_GENRE',
            'PANTS TYPE' => 'VCH_PANTS_TYPE',
            'COMPOSITION' => 'VCH_COMPOSITION',
            'MATERIAL VESTUARIO' => 'VCH_MATERIAL_VESTUARIO',
            'CONDICION PRODUCTO' => 'VCH_CONDICION_PRODUCTO',
            'GARANTIA PRODUCTO' => 'VCH_GARANTIA_PRODUCTO',
            'GARANTIA VENDEDOR' => 'VCH_GARANTIA_VENDEDOR',
            'CONTENIDO PAQUETE' => 'VCH_CONTENIDO_PAQUETE',
            'ANCHO PAQUETE' => 'NUM_ANCHO_PAQUETE',
            'LARGO PAQUETE' => 'NUM_LARGO_PAQUETE',
            'ALTO PAQUETE' => 'NUM_ALTO_PAQUETE',
            'PESO PAQUETE' => 'NUM_PESO_PAQUETE',
            'IMAGEN PRINCIPAL' => 'VCH_IMAGEN_PRINCIPAL',
            'IMAGEN2' => 'VCH_IMAGEN2',
            'IMAGEN3' => 'VCH_IMAGEN3',
            'IMAGEN4' => 'VCH_IMAGEN4',
            'IMAGEN5' => 'VCH_IMAGEN5',
            'IMAGEN6' => 'VCH_IMAGEN6',
            'IMAGEN7' => 'VCH_IMAGEN7',
            'IMAGEN8' => 'VCH_IMAGEN8',
            'MONEDA' => 'VCH_MONEDA',
            'TIPO PUBLI' => 'VCH_TIPO_PUBLI',
            'FORM ENV' => 'VCH_FORM_ENV',
            'COSTO EN' => 'VCH_COSTO_EN',
            'RETIRO' => 'VCH_RETIRO',
            'PESO PROD' => 'VCH_PESO_PROD',
            'LONG PROD' => 'VCH_LONG_PROD',
            'ANCHO PROD' => 'VCH_ANCHO_PROD',
            'ALTURA PROD' => 'VCH_ALTURA_PROD',
            'TIPO CUE' => 'VCH_TIPO_CUE',
            'TIPO PUN' => 'VCH_TIPO_PUN',
            'TIPO CIER' => 'VCH_TIPO_CIER',
            'TIPO GARANT' => 'VCH_TIPO_GARANT',
            'TABLA TALLA' => 'VCH_TABLA_TALLA',
            'TAMANIO PROD' => 'VCH_TAMANIO_PROD'
        ];

        return $mapeo[$encabezadoUpper] ?? null;
    }

    /*---------- Obtener Estadísticas ----------*/
    public function obtenerEstadisticasControlador()
    {
        $stats = $this->productoModel->obtenerEstadisticasModelo();
        return json_encode([
            "status" => "ok",
            "data" => $stats
        ]);
    }
}
