<?php

namespace app\models;

class productoModel extends mainModel
{

    /*---------- Obtener siguiente ID disponible ----------*/
    public function obtenerSiguienteIdProducto()
    {
        $sql = "SELECT NUM_ID_PRODUCTO + 1 AS next_id 
                FROM producto 
                WHERE NUM_ID_PRODUCTO + 1 NOT IN (SELECT NUM_ID_PRODUCTO FROM producto) 
                ORDER BY NUM_ID_PRODUCTO 
                LIMIT 1";

        $result = $this->ejecutarConsulta($sql);

        if ($result->rowCount() > 0) {
            $row = $result->fetch();
            return $row['next_id'];
        } else {
            $sql = "SELECT COALESCE(MAX(NUM_ID_PRODUCTO), 0) + 1 AS next_id FROM producto";
            $result = $this->ejecutarConsulta($sql);
            $row = $result->fetch();
            return $row['next_id'];
        }
    }

    /*---------- Registrar Producto ----------*/
    public function registrarProductoModelo($datos)
    {
        $sql = $this->guardarDatos("producto", $datos);
        return $sql;
    }

    /*---------- Actualizar Producto ----------*/
    public function actualizarProductoModelo($datos, $condicion)
    {
        $sql = $this->actualizarDatos("producto", $datos, $condicion);
        return $sql;
    }

    /*---------- Eliminar Producto ----------*/
    public function eliminarProductoModelo($id)
    {
        $sql = $this->eliminarRegistro("producto", "NUM_ID_PRODUCTO", $id);
        return $sql;
    }

    /*---------- Buscar Producto por ID ----------*/
    public function buscarProductoPorIdModelo($id)
    {
        $sql = $this->seleccionarDatos("Unico", "producto", "NUM_ID_PRODUCTO", $id);
        return $sql;
    }

    /*---------- Listar todos los Productos ----------*/
    public function listarTodosProductosModelo()
    {
        $sql = $this->ejecutarConsulta("SELECT p.*, 
            c.VCH_NOMBRE AS NOMBRE_CLASE
            FROM producto p
            LEFT JOIN clase c ON p.NUM_ID_CLASE = c.NUM_ID_CLASE
            ORDER BY p.NUM_ID_PRODUCTO ASC");
        return $sql;
    }


    /*---------- Listar Productos con Filtros ----------*/
    public function listarProductosFiltrosModelo($idClase = 0, $busqueda = '')
    {
        $sql = "SELECT p.*, 
        c.VCH_NOMBRE AS NOMBRE_CLASE
        FROM producto p
        LEFT JOIN clase c ON p.NUM_ID_CLASE = c.NUM_ID_CLASE
        WHERE 1=1";

        // Solo filtrar por clase (producto NO tiene tienda)
        if ($idClase > 0) {
            $sql .= " AND p.NUM_ID_CLASE = $idClase";
        }

        // Filtro de búsqueda
        if (!empty($busqueda)) {
            $sql .= " AND (
            p.VCH_NOMBRE LIKE '%$busqueda%' OR
            p.VCH_MARCA LIKE '%$busqueda%' OR
            p.VCH_MODELO LIKE '%$busqueda%' OR
            p.VCH_SKU_VENDEDOR LIKE '%$busqueda%' OR
            p.VCH_CODIGO_BARRAS LIKE '%$busqueda%'
        )";
        }

        $sql .= " ORDER BY p.NUM_ID_PRODUCTO DESC";

        return $this->ejecutarConsulta($sql);
    }
   

    /*---------- Obtener columnas dinámicas según clase y tienda ----------*/
    public function obtenerColumnasDinamicasModelo($idClase, $idTienda = 0)
    {
        $sql = "SELECT 
            pd.VCH_CAMPO AS CAMPO_ETIQUETA,
            pd.VCH_NOMBRE_PLANTILLA AS CAMPO_BD,
            pd.VCH_OBLIGATORIO AS OBLIGATORIO,
            pd.NUM_ORDEN as ORDEN
            FROM plant_detalle pd 
            INNER JOIN plantilla p ON pd.NUM_ID_PLANTILLA = p.NUM_ID_PLANTILLA
            WHERE p.NUM_ID_CLASE = :IdClase
            AND pd.VCH_ESTADO = 1
            AND p.VCH_ESTADO = 1";

        if ($idTienda > 0) {
            $sql .= " AND p.NUM_ID_TIENDA = :IdTienda";
        }

        $sql .= " ORDER BY pd.NUM_ORDEN ASC";

        $stmt = $this->conectar()->prepare($sql);
        $stmt->bindParam(":IdClase", $idClase, \PDO::PARAM_INT);
        if ($idTienda > 0) {
            $stmt->bindParam(":IdTienda", $idTienda, \PDO::PARAM_INT);
        }
        $stmt->execute();

        return $stmt;
    }
   
    /*---------- Verificar si plantilla existe para clase/tienda ----------*/
    public function verificarPlantillaExisteModelo($idClase, $idTienda = 0)
    {
        $sql = "SELECT p.NUM_ID_PLANTILLA
            FROM plantilla p
            WHERE p.NUM_ID_CLASE = :IdClase
            AND p.VCH_ESTADO = 1";

        if ($idTienda > 0) {
            $sql .= " AND p.NUM_ID_TIENDA = :IdTienda";
        }

        $sql .= " AND EXISTS (
                SELECT 1 FROM plant_detalle pd 
                WHERE pd.NUM_ID_PLANTILLA = p.NUM_ID_PLANTILLA
                AND pd.VCH_ESTADO = 1
            )
            LIMIT 1";

        $stmt = $this->conectar()->prepare($sql);
        $stmt->bindParam(":IdClase", $idClase, \PDO::PARAM_INT);
        if ($idTienda > 0) {
            $stmt->bindParam(":IdTienda", $idTienda, \PDO::PARAM_INT);
        }
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }


    /*---------- Obtener opciones de lista por juego ----------*/
    public function obtenerOpcionesListaModelo($juego)
    {
        $sql = "SELECT  l.VCH_CODIGO
                FROM listas l
                WHERE l.VCH_JUEGO = :Juego
                AND l.VCH_ESTADO = 1
                ORDER BY l.VCH_CODIGO";

        $stmt = $this->conectar()->prepare($sql);
        $stmt->bindParam(":Juego", $juego);
        $stmt->execute();

        return $stmt;
    }

    /*---------- Obtener estadísticas de productos ----------*/
    public function obtenerEstadisticasModelo()
    {
        $sql = "SELECT 
                COUNT(*) as total_productos,
                SUM(CASE WHEN NUM_STOCK > 0 THEN 1 ELSE 0 END) as con_stock,
                SUM(CASE WHEN NUM_SALE_PRICE_FALABELLA > 0 
                    AND NUM_SALE_PRICE_FALABELLA < NUM_PRICE_FALABELLA 
                    THEN 1 ELSE 0 END) as con_oferta,
                AVG(CASE WHEN NUM_PRICE_FALABELLA > 0 THEN NUM_PRICE_FALABELLA ELSE NULL END) as precio_promedio
                FROM producto";

        $result = $this->ejecutarConsulta($sql);
        return $result->fetch();
    }

    /*---------- Insertar productos desde CSV ----------*/
    public function insertarProductosCSVModelo($productos)
    {
        $insertados = 0;
        $errores = [];

        foreach ($productos as $index => $producto) {
            try {
                // Validar que tenga campos obligatorios
                $tieneClase = false;
                $tieneNombre = false;
                $tienePrecio = false;

                foreach ($producto as $campo) {
                    if ($campo['campo_nombre'] == 'NUM_ID_CLASE' && !empty($campo['campo_valor'])) {
                        $tieneClase = true;
                    }
                    if ($campo['campo_nombre'] == 'VCH_NOMBRE' && !empty($campo['campo_valor'])) {
                        $tieneNombre = true;
                    }
                    if ($campo['campo_nombre'] == 'NUM_PRICE_FALABELLA' && !empty($campo['campo_valor'])) {
                        $tienePrecio = true;
                    }
                }

                if (!$tieneClase || !$tieneNombre || !$tienePrecio) {
                    $errores[] = "Producto " . ($index + 1) . ": Faltan campos obligatorios";
                    continue;
                }

                $sql = $this->guardarDatos("producto", $producto);
                if ($sql->rowCount() > 0) {
                    $insertados++;
                } else {
                    $errores[] = "Producto " . ($index + 1) . ": No se pudo insertar";
                }
            } catch (\Exception $e) {
                $errores[] = "Producto " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        return [
            'insertados' => $insertados,
            'errores' => $errores
        ];
    }

    /*---------- Obtener todas las columnas de la tabla producto ----------*/
    public function obtenerColumnasTablaModelo()
    {
        $sql = "DESCRIBE producto";
        return $this->ejecutarConsulta($sql);
    }

    

    /*----------  Obtener productos con columnas dinámicas según plantilla ----------*/
    public function obtenerProductosConPlantillaModelo($idClase, $columnas, $idTienda = 0)
    {
        if (empty($columnas)) {
            error_log("Error: columnas vacías");
            return false;
        }

        // Validar que las columnas existen en la tabla
        $columnasSeguras = [];
        foreach ($columnas as $col) {
            // Solo permitir nombres de columna válidos (sin SQL injection)
            if (preg_match('/^[A-Z_0-9]+$/', $col)) {
                $columnasSeguras[] = "p." . $col;
            }
        }

        if (empty($columnasSeguras)) {
            error_log("Error: No hay columnas seguras después de validación");
            return false;
        }

        $columnasStr = implode(', ', $columnasSeguras);

        $sql = "SELECT $columnasStr 
            FROM producto p
            WHERE p.NUM_ID_CLASE = :IdClase";

        if ($idTienda > 0) {
            $sql .= " AND p.NUM_ID_TIENDA = :IdTienda";
        }

        $sql .= " ORDER BY p.NUM_ID_PRODUCTO";

        // Log para debug
        error_log("SQL generado: " . $sql);
        error_log("ID Clase: " . $idClase);

        try {
            $stmt = $this->conectar()->prepare($sql);
            $stmt->bindParam(":IdClase", $idClase, \PDO::PARAM_INT);
            if ($idTienda > 0) {
                $stmt->bindParam(":IdTienda", $idTienda, \PDO::PARAM_INT);
            }
            $stmt->execute();

            // Verificar si hay resultados
            $rowCount = $stmt->rowCount();
            error_log("Productos encontrados: " . $rowCount);

            return $stmt;
        } catch (\Exception $e) {
            error_log("Error en obtenerProductosConPlantillaModelo: " . $e->getMessage());
            error_log("SQL que falló: " . $sql);
            return false;
        }
    }

    /*---------- Limpiar cadena (heredado de mainModel) ----------*/
    public function limpiarCadena($cadena)
    {
        $cadena = trim($cadena);
        $cadena = stripslashes($cadena);
        $cadena = str_ireplace("<script>", "", $cadena);
        $cadena = str_ireplace("</script>", "", $cadena);
        $cadena = str_ireplace("<script src", "", $cadena);
        $cadena = str_ireplace("<script type=", "", $cadena);
        $cadena = str_ireplace("SELECT * FROM", "", $cadena);
        $cadena = str_ireplace("DELETE FROM", "", $cadena);
        $cadena = str_ireplace("INSERT INTO", "", $cadena);
        $cadena = str_ireplace("DROP TABLE", "", $cadena);
        $cadena = str_ireplace("DROP DATABASE", "", $cadena);
        $cadena = str_ireplace("TRUNCATE TABLE", "", $cadena);
        $cadena = str_ireplace("SHOW TABLES", "", $cadena);
        $cadena = str_ireplace("SHOW DATABASES", "", $cadena);
        $cadena = str_ireplace("<?php", "", $cadena);
        $cadena = str_ireplace("?>", "", $cadena);
        $cadena = str_ireplace("--", "", $cadena);
        $cadena = str_ireplace("^", "", $cadena);
        $cadena = str_ireplace("[", "", $cadena);
        $cadena = str_ireplace("]", "", $cadena);
        $cadena = str_ireplace("==", "", $cadena);
        $cadena = str_ireplace(";", "", $cadena);
        $cadena = str_ireplace("::", "", $cadena);
        $cadena = trim($cadena);
        return $cadena;
    }

    /*---------- Obtener ID de plantilla según clase y tienda ----------*/
    public function obtenerIdPlantillaModelo($idClase, $idTienda = 0)
    {
        $sql = "SELECT NUM_ID_PLANTILLA 
            FROM plantilla 
            WHERE NUM_ID_CLASE = :IdClase";

        if ($idTienda > 0) {
            $sql .= " AND NUM_ID_TIENDA = :IdTienda";
        }

        $sql .= " AND VCH_ESTADO = 1
              LIMIT 1";

        $stmt = $this->conectar()->prepare($sql);
        $stmt->bindParam(":IdClase", $idClase, \PDO::PARAM_INT);
        if ($idTienda > 0) {
            $stmt->bindParam(":IdTienda", $idTienda, \PDO::PARAM_INT);
        }
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            return $row['NUM_ID_PLANTILLA'];
        }

        return null;
    }

    /*----------  Obtener columnas por ID de plantilla ----------*/
    public function obtenerColumnasPorPlantillaModelo($idPlantilla)
    {
        $sql = "SELECT 
            pd.VCH_CAMPO AS CAMPO_ETIQUETA,
            pd.VCH_NOMBRE_PLANTILLA AS CAMPO_BD,
            pd.VCH_OBLIGATORIO AS OBLIGATORIO,
            pd.NUM_ORDEN AS ORDEN
            FROM plant_detalle pd 
            WHERE pd.NUM_ID_PLANTILLA = :IdPlantilla
            AND pd.VCH_ESTADO = 1
            ORDER BY pd.NUM_ORDEN ASC";

        $stmt = $this->conectar()->prepare($sql);
        $stmt->bindParam(":IdPlantilla", $idPlantilla, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    }



    public function obtenerCamposPlantillaModelo($idClase, $idTienda = 0)

    {

        $sql = "SELECT 

        pd.VCH_NOMBRE_PLANTILLA AS encabezado,
        pd.VCH_NOMBRE_PLANTILLA AS columna_producto,
        pd.VCH_OBLIGATORIO AS obligatorio,
        pd.VCH_JUEGO AS juego_lista,
        pd.NUM_ORDEN AS orden

    FROM plant_detalle pd

    INNER JOIN plantilla pl ON pd.NUM_ID_PLANTILLA = pl.NUM_ID_PLANTILLA
    WHERE pl.NUM_ID_CLASE = :IdClase
      AND pl.VCH_ESTADO = 1 
      AND pd.VCH_ESTADO = 1";



        if ($idTienda > 0) {

            $sql .= " AND pl.NUM_ID_TIENDA = :IdTienda";
        }

        $sql .= " GROUP BY 

        pd.VCH_NOMBRE_PLANTILLA,

        pd.VCH_NOMBRE_PLANTILLA,

        pd.VCH_OBLIGATORIO,

        pd.VCH_JUEGO,

        pd.NUM_ORDEN

    ORDER BY MIN(pd.NUM_ORDEN) ASC";



        $stmt = $this->conectar()->prepare($sql);

        $stmt->bindParam(":IdClase", $idClase, \PDO::PARAM_INT);

        if ($idTienda > 0) {

            $stmt->bindParam(":IdTienda", $idTienda, \PDO::PARAM_INT);
        }

        $stmt->execute();



        return $stmt;
    }





    /*----------: Obtener opciones de lista segÃºn VCH_JUEGO (SIN DUPLICADOS) ----------*/

    public function obtenerOpcionesListaPorJuegoModelo($juego, $idTienda = 0)

    {

        $sql = "SELECT DISTINCT
        VCH_CODIGO
    FROM listas
    WHERE VCH_JUEGO = :Juego
      AND VCH_ESTADO = 1";



        if ($idTienda > 0) {

            $sql .= " AND NUM_ID_TIENDA = :IdTienda";
        }



        $sql .= " ORDER BY VCH_CODIGO ASC";



        $stmt = $this->conectar()->prepare($sql);

        $stmt->bindParam(":Juego", $juego);

        if ($idTienda > 0) {

            $stmt->bindParam(":IdTienda", $idTienda, \PDO::PARAM_INT);
        }

        $stmt->execute();



        return $stmt;
    }
}
