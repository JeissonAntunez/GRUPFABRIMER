<?php

namespace app\models;

class imagenModel extends mainModel
{
    /*---------- Buscar producto por SKU padre ----------*/
    public function buscarProductoPorSkuPadre($sku_padre)
    {
        try {
            $sql = $this->conectar()->prepare("
                SELECT * FROM producto 
                WHERE VCH_SKU_PADRE = :SkuPadre 
                LIMIT 1
            ");
            $sql->bindParam(":SkuPadre", $sku_padre);
            $sql->execute();
            return $sql;
        } catch (\PDOException $e) {
            error_log("Error en buscarProductoPorSkuPadre: " . $e->getMessage());
            return false;
        }
    }

    /*---------- Obtener TODOS los productos con un SKU padre ----------*/
    public function obtenerProductosPorSkuPadre($sku_padre)
    {
        try {
            $sql = $this->conectar()->prepare("
                SELECT 
                    NUM_ID_PRODUCTO,
                    VCH_NOMBRE,
                    VCH_SKU_VENDEDOR,
                    VCH_SKU_PADRE,
                    VCH_IMAGEN_PRINCIPAL,
                    VCH_IMAGEN2,
                    VCH_IMAGEN3,
                    VCH_IMAGEN4,
                    VCH_IMAGEN5,
                    VCH_IMAGEN6,
                    VCH_IMAGEN7,
                    VCH_IMAGEN8
                FROM producto 
                WHERE VCH_SKU_PADRE = :SkuPadre
                ORDER BY NUM_ID_PRODUCTO ASC
            ");
            $sql->bindParam(":SkuPadre", $sku_padre);
            $sql->execute();
            return $sql;
        } catch (\PDOException $e) {
            error_log("Error en obtenerProductosPorSkuPadre: " . $e->getMessage());
            return false;
        }
    }

    /*---------- Contar productos por SKU padre ----------*/
    public function contarProductosPorSkuPadre($sku_padre)
    {
        try {
            $sql = $this->conectar()->prepare("
                SELECT COUNT(*) as total
                FROM producto 
                WHERE VCH_SKU_PADRE = :SkuPadre
            ");
            $sql->bindParam(":SkuPadre", $sku_padre);
            $sql->execute();
            $resultado = $sql->fetch();
            return $resultado['total'];
        } catch (\PDOException $e) {
            error_log("Error en contarProductosPorSkuPadre: " . $e->getMessage());
            return 0;
        }
    }

    /*---------- Verificar si existe SKU padre ----------*/
    public function existeSkuPadre($sku_padre)
    {
        try {
            $sql = $this->conectar()->prepare("
                SELECT COUNT(*) as total
                FROM producto 
                WHERE VCH_SKU_PADRE = :SkuPadre
            ");
            $sql->bindParam(":SkuPadre", $sku_padre);
            $sql->execute();
            $resultado = $sql->fetch();
            return $resultado['total'] > 0;
        } catch (\PDOException $e) {
            error_log("Error en existeSkuPadre: " . $e->getMessage());
            return false;
        }
    }

    /*---------- Obtener lista de SKUs padre únicos ----------*/
    public function listarSkusPadre()
    {
        try {
            $sql = $this->conectar()->prepare("
                SELECT DISTINCT 
                    VCH_SKU_PADRE,
                    COUNT(*) as total_variantes
                FROM producto 
                WHERE VCH_SKU_PADRE IS NOT NULL 
                  AND VCH_SKU_PADRE != ''
                GROUP BY VCH_SKU_PADRE
                ORDER BY VCH_SKU_PADRE ASC
            ");
            $sql->execute();
            return $sql;
        } catch (\PDOException $e) {
            error_log("Error en listarSkusPadre: " . $e->getMessage());
            return false;
        }
    }

    /*---------- Obtener estadísticas de imágenes ----------*/
    public function obtenerEstadisticasImagenes()
    {
        try {
            $sql = $this->conectar()->prepare("
                SELECT 
                    COUNT(DISTINCT VCH_SKU_PADRE) as total_skus_padre,
                    SUM(CASE WHEN VCH_IMAGEN_PRINCIPAL IS NOT NULL THEN 1 ELSE 0 END) as con_imagen_principal,
                    SUM(CASE WHEN VCH_IMAGEN2 IS NOT NULL THEN 1 ELSE 0 END) as con_imagen_2,
                    SUM(CASE WHEN VCH_IMAGEN3 IS NOT NULL THEN 1 ELSE 0 END) as con_imagen_3,
                    SUM(CASE WHEN VCH_IMAGEN4 IS NOT NULL THEN 1 ELSE 0 END) as con_imagen_4,
                    SUM(CASE WHEN VCH_IMAGEN5 IS NOT NULL THEN 1 ELSE 0 END) as con_imagen_5,
                    SUM(CASE WHEN VCH_IMAGEN6 IS NOT NULL THEN 1 ELSE 0 END) as con_imagen_6,
                    SUM(CASE WHEN VCH_IMAGEN7 IS NOT NULL THEN 1 ELSE 0 END) as con_imagen_7,
                    SUM(CASE WHEN VCH_IMAGEN8 IS NOT NULL THEN 1 ELSE 0 END) as con_imagen_8
                FROM producto
                WHERE VCH_SKU_PADRE IS NOT NULL 
                  AND VCH_SKU_PADRE != ''
            ");
            $sql->execute();
            return $sql->fetch();
        } catch (\PDOException $e) {
            error_log("Error en obtenerEstadisticasImagenes: " . $e->getMessage());
            return false;
        }
    }
}